<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="100" class="search-form">
        <FormItem label="积分日期" prop="date_time">
            <DatePicker type="date" placeholder="积分日期" v-model="searchForm.date_time" style="width: 200px">
            </DatePicker>
        </FormItem>
        <!-- <FormItem label="部门" prop="department_name">
          <Cascader v-model="searchForm.department_name" :data="department_data" filterable style="width:450px"></Cascader>
        </FormItem> -->
        <FormItem style="margin-left:-70px;" class="br">
          <Button @click="getVideoPatrolList" type="primary" icon="ios-search">检索</Button>
        </FormItem>
      </Form>
    </Row>
    <Table type="selection" stripe border :columns="columns" :data="data" :loading="tableLoading"></Table>
    <Row type="flex" justify="end" class="page">
      <Page
        :total="dataCount"
        :page-size="searchForm.pageSize"
        @on-change="changePage"
        @on-page-size-change="changePageSize"
        show-total
        :current="searchForm.pageNumber"/>
    </Row>
    <Modal
      :mask-closable="false"
      v-model="editModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="修改营业情况">
      <Form ref="editForm" :model="editForm" :label-width="90" :rules="editFormValidate">
        <Row>
          <Col span="12">
            <FormItem label="渠道名称" prop="title">
              <Input v-model="editForm.title" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="联系人" prop="name">
              <Input v-model="editForm.name" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="所在地区" prop="area">
              <Input v-model="editForm.area" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="详细地址" prop="addr">
              <Input v-model="editForm.addr" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="门店状态" prop="shop_state">
              <i-switch size="large" v-model="editForm.shop_state" :true-value=1 :false-value=0>
                <span slot="open">正常营业</span>
                <span slot="close">未营业</span>
              </i-switch>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="情况说明" prop="desc">
              <Input v-model="editForm.desc" type="text" placeholder=""/>
            </FormItem>
          </Col>
        </Row>
      </Form>
      <div slot="footer" class="footer_float">
        <Button @click="handleReset('editForm')" :loading="loading">重置</Button>
        <Button
          @click="editSubmit('editForm')"
          :loading="submitLoading"
          type="primary"
          style="margin-left:8px"
        >保存
        </Button>
      </div>
      <div class="clear"></div>
    </Modal>
  </Card>
</template>
<script>
  import {
    departmentList,
    videoPatrolList,dictData,videoPatrolEdit,videoPatrolDel 
  } from '../../../api/value';
  import './videoPatrolList.css'

  export default {
    data() {
      return {
        dataCount: 0,   // 总条数
        columns: [
          {
            type: 'index2',
            width: 50,
            align: 'center',
            fixed: 'left',
            render: (h, params) => {
              return h('span', params.index + (this.searchForm.pageNumber - 1) * this.searchForm.pageSize + 1);
            }
          },
          {
            title: '渠道名称',
            key: 'title',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '联系人',
            key: 'name',
            width: 100,
            align: 'center'
          },
          {
            title: '联系电话',
            key: 'mobile',
            width: 150,
            align: "center"
          },
          {
            title: '所在地区',
            key: 'area',
            width: 200,
            align: "center"
          },
          {
            title: '详细地址',
            key: 'addr',
            width: 200,
            align: "center"
          },
          {
            title: '营业情况',
            key: 'shop_state',
            width: 300,
            align: "center"
          },
          {
            title: '情况说明',
            key: 'desc',
            width: 180,
            align: "center"
          },
          {
            title: '填报人',
            key: 'applicant',
            width: 180,
            align: "center"
          },
          {
            title: '日期',
            key: 'date_time',
            width: 180,
            align: "center"
          },
          {
            title: '操作',
            key: 'action',
            width: 180,
            fixed: 'right',
            align: 'center',
            render: (h, params) => {
              let editButton;
              let delButton;
              return h('div', [
                h('Button', {
                  props: {
                    type: 'primary',
                    size: 'small',
                    disabled: editButton,
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      console.log(params.row);
                      this.editForm.title=params.row.title;
                      this.editForm.name=params.row.name;
                      this.editForm.mobile=params.row.mobile;
                      this.editForm.area=params.row.area;
                      this.editForm.addr=params.row.addr;
                      this.editForm.desc=params.row.desc;
                      this.editForm.shop_state=params.row.shop_state=="正在营业"?1:0;
                      this.editForm.id=params.row.id;
                      this.editModal = true;
                    }
                  }
                }, '编辑'),
                h('Button', {
                  props: {
                    type: 'error',
                    size: 'small',
                    disabled: delButton,
                    // loading: _this.editFormLoading
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      this.$Modal.confirm({
                        title: "确认删除",
                        loading: true,
                        content: "您确认要删除这个数据？",
                        onOk: () => {
                          videoPatrolDel({id: params.row.id}).then(res => {
                            if (res.result === true) {
                              this.$Message.success("删除成功");
                              this.init();
                            } else {
                              this.$Message.error("删除失败");
                            }
                            this.$Modal.remove();
                          });
                        }
                      });
                    }
                  }
                }, '删除')
              ])
            }
          }
        ],
        data: [],
        tableLoading: true,
        loading: false,
        editButton:false,
        searchForm: {
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        submitLoading: false,
        modal: false,
        editModal: false,
        editForm: {
          title: '',
          name: '',
          mobile: '',
          area: '',
          addr: '',
          shop_state: '',
          desc:'',
          id:''
        },
        editFormValidate: {
          // 表单验证规则
          mobile: [
            {required: true, message: "用户号码不能为空"}
          ]
        },
        lists:[],
        project_type: [],
        department_data:[]
      }
    },
    methods: {
      init() {
        this.getVideoPatrolList();
        // this.getDepartmentList();
      },
      getVideoPatrolList() {
        this.tableLoading = true;
        videoPatrolList(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
        })
      },
      getDepartmentList() {
        this.loading = true;
        departmentList(this.searchForm).then(res => {
          this.loading = false;
          if (res.result) {
            this.department_data=res.result
          }
        });
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getVideoPatrolList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getVideoPatrolList();
      },
      cancel() {
        this.$refs.editForm.resetFields();
        this.editForm.shop_state=0
      },
      handleReset(name) {
        this.$refs[name].resetFields();
        this.editForm.shop_state=0
      },
      editSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            videoPatrolEdit(this.editForm).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("修改成功");
                this.editModal = false;
                this.getVideoPatrolList();
                this.cancel();
              } else {
                this.$Message.error('修改失败!');
              }
            });
          }
        })
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
