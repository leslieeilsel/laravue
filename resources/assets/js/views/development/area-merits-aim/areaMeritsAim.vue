<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">片区目标填报</Button>
    </p>
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
    <!-- model -->
    <Modal
      :mask-closable="false"
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="销售填报">
      <Form ref="form" :model="form" :label-width="160">
        <Row>
          <Col span="12">
            <FormItem label="目标责任部门" prop="duty_department">
              <Cascader v-model="form.duty_department" :data="department_data" filterable></Cascader>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="目标责任人" prop="duty_user">
              <Input v-model="form.duty_user" placeholder="" ></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="目标开始时间" prop="target_start_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.target_start_time"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="目标结束时间" prop="target_end_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.target_end_time"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="业务类型" prop="business_type">
              <Select v-model="form.business_type" filterable>
                <Option v-for="item in dict.business_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <!-- <Col span="12">
            <FormItem label="产品类型" prop="product_type">
              <Select v-model="form.product_type" filterable>
                <Option v-for="item in dict.product_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col> -->
          <Col span="12">
            <FormItem label="销售目标" prop="target">
              <Input type="number" v-model="form.target"></Input>
            </FormItem>
          </Col>
        </Row>
      </Form>
      <div slot="footer">
        <Button @click="handleReset('form')" :loading="loading">重置</Button>
        <Button
          @click="submitF('form')"
          :loading="submitLoading"
          type="primary"
          style="margin-left:8px"
        >保存
        </Button>
      </div>
    </Modal>
    <Modal
      :mask-closable="false"
      v-model="editModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="修改片区目标">
      <Form ref="editForm" :model="editForm" :label-width="90" :rules="editFormValidate">
        <Row>
          <Col span="12">
            <FormItem label="目标责任部门" prop="duty_department">
              <Cascader v-model="editForm.duty_department" :data="department_data" filterable></Cascader>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="目标责任人" prop="duty_user">
              <Input v-model="editForm.duty_user" placeholder="" ></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="目标开始时间" prop="target_start_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="editForm.target_start_time"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="目标结束时间" prop="target_end_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="editForm.target_end_time"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="业务类型" prop="business_type">
              <Select v-model="editForm.business_type" filterable>
                <Option v-for="item in dict.business_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <!-- <Col span="12">
            <FormItem label="产品类型" prop="product_type">
              <Select v-model="form.product_type" filterable>
                <Option v-for="item in dict.product_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col> -->
          <Col span="12">
            <FormItem label="销售目标" prop="target">
              <Input type="number" v-model="editForm.target"></Input>
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
    areaMeritsAimAdd,
    areaMeritsAimList,
    dictData,
    areaDepartmentList,
    departmentInfo,
    areaMeritsAimEdit,
    areaMeritsAimDel
  } from '../../../api/value';
  import './areaMeritsAim.css'

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
            title: '目标责任部门',
            key: 'duty_department',
            width: 200,
            // fixed: 'left',
            align: "center"
          },
          {
            title: '目标责任人',
            key: 'duty_user',
            fixed: 'center',
            width: 220,
          },
          {
            title: '目标开始时间',
            key: 'target_start_time',
            width: 240,
            align: 'center'
          },
          {
            title: '目标结束时间',
            key: 'target_start_time',
            width: 240,
            align: 'center'
          },
          {
            title: '目标时间',
            key: 'target_start_time',
            width: 240,
            align: 'center'
          },
          {
            title: '业务类型',
            key: 'business_type',
            width: 200,
            align: "center"
          },
          {
            title: '销售目标',
            key: 'target',
            width: 200,
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
                      let duty_department_id = JSON.parse( params.row.duty_department_id );
                      this.editForm.id=params.row.id;
                      this.editForm.duty_department=duty_department_id;
                      this.editForm.duty_user=params.row.duty_user;
                      this.editForm.target_end_time=params.row.target_end_time;
                      this.editForm.target_start_time=params.row.target_start_time;
                      this.editForm.target=params.row.target;
                      this.editForm.business_type=parseInt(params.row.business_type_id);
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
                          areaMeritsAimDel({id: params.row.id}).then(res => {
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
        modal: false,
        editModal:false,
        searchForm: {
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        submitLoading: false,
        form: {
          duty_department: [],
          duty_user: '',
          target_start_time: '',
          target_end_time: '',
          target: '',
          business_type: '',
        },
        editForm: {
          duty_department: [],
          duty_user: '',
          target_start_time: '',
          target_end_time: '',
          target: '',
          business_type: '',
        },
        editFormValidate: {
        },
        dictName: {
          // product_type: '产品类型',
          business_type: '业务类型',
        },
        dict: {
          // product_type: [],
          business_type: []
        },
        department_data:[],
      }
    },
    methods: {
      init() {
        this.getAreaMeritsAimList();
        this.getDictData();
        this.getDepartmentList();
      },
      getDepartmentList() {
        this.loading = true;
        areaDepartmentList().then(res => {
          this.loading = false;
          if (res.result) {
            this.department_data=res.result
          }
        });
      },
      getAreaMeritsAimList() {
        this.tableLoading = true;
        areaMeritsAimList(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          // this.searchForm.pageNumber = 1;
          // this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getAreaMeritsAimList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getAreaMeritsAimList();
      },
      handleReset(name) {
        this.$refs[name].resetFields();
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            areaMeritsAimAdd(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("片区目标填报成功");
                this.modal = false;
                this.init();
              } else {
                this.$Message.error('片区目标填报失败!');
              }
            });
          }
        })
      },
      cancel() {
        this.$refs.form.resetFields();
      },
      getDictData() {
        dictData(this.dictName).then(res => {
          console.log(res);
          if (res.result) {
            this.dict = res.result;
          }
        });
      },
      editSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            areaMeritsAimEdit(this.editForm).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("活动计划修改成功");
                this.editModal = false;
                this.init();
              } else {
                this.$Message.error('活动计划修改失败!');
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
