<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">活动计划填报</Button>
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
      :styles="{top: '150px'}"
      width="850"
      title="活动计划填报">
      <Form ref="form" :model="form" :label-width="160" :rules="formValidate">
        <Row>
          <Col span="12">
            <FormItem label="活动名称" prop="title">
              <Input v-model="form.title" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动地点" prop="position">
              <Input v-model="form.position" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="活动开始时间" prop="plan_start_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.plan_start_time"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动结束时间" prop="plan_end_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.plan_end_time"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="区域" prop="area">
              <Cascader v-model="form.area" :data="department_data" filterable ></Cascader>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动文字描述" prop="remark">
              <Input v-model="form.remark" type="textarea" :rows="3" placeholder="请输入..."></Input>
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
      title="修改活动计划">
      <Form ref="editForm" :model="editForm" :label-width="90" :rules="editFormValidate">
        <Row>
          <Col span="12">
            <FormItem label="活动名称" prop="title">
              <Input v-model="editForm.title" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动地点" prop="position">
              <Input v-model="editForm.position" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="活动开始时间" prop="plan_start_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="editForm.plan_start_time"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动结束时间" prop="plan_end_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="editForm.plan_end_time"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="区域" prop="area">
              <Cascader v-model="editForm.area" :data="department_data" filterable ></Cascader>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动文字描述" prop="remark">
              <Input v-model="editForm.remark" type="textarea" :rows="3" placeholder="请输入..."></Input>
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
    activityPlan,activityPlanAdd,activityPlanEdit,activityPlanDel,areaDepartmentList
  } from '../../../api/value';
  import './activityPlan.css'

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
            title: '活动名称',
            key: 'title',
            width: 200,
            // fixed: 'left',
            align: "center"
          },
          {
            title: '活动地点',
            key: 'position',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '负责人',
            key: 'applicant',
            width: 100,
            align: 'left'
          },
          {
            title: '区域',
            key: 'area',
            width: 200,
            align: "left"
          },
          {
            title: '活动开始时间',
            key: 'plan_start_time',
            width: 200,
            align: "left"
          },
          {
            title: '活动结束时间',
            key: 'plan_end_time',
            width: 200,
            align: "left"
          },
          {
            title: '活动文字描述',
            key: 'remark',
            width: 300,
            align: "left"
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
                      let area_id = JSON.parse( params.row.area_id );
                      this.editForm.id=params.row.id;
                      this.editForm.area=area_id;
                      this.editForm.plan_end_time=params.row.plan_end_time;
                      this.editForm.plan_start_time=params.row.plan_start_time;
                      this.editForm.position=params.row.position;
                      this.editForm.remark=params.row.remark;
                      this.editForm.title=params.row.title;
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
                          activityPlanDel({id: params.row.id}).then(res => {
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
        searchForm: {
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        submitLoading: false,
        modal: false,
        editModal:false,
        form: {
          title: '',
          position: '',
          area: [],
          plan_start_time: '',
          plan_end_time: '',
          remark: ''
        },
        formValidate: {
          // 表单验证规则
          title: [
            {required: true, message: "活动名称为空"}
          ]
        },
        editForm:{
          title: '',
          position: '',
          area: [],
          plan_start_time: '',
          plan_end_time: '',
          remark: '',
          id:0,
          area_id: [],
        },
        editFormValidate: {
          // 表单验证规则
          title: [
            {required: true, message: "活动名称为空"}
          ]
        },
        department_data:[],
      }
    },
    methods: {
      init() {
        this.getActivityPlan();
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
      getActivityPlan() {
        this.tableLoading = true;
        activityPlan(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          // this.searchForm.pageNumber = 1;
          // this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getActivityPlan();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getActivityPlan();
      },
      handleReset(name) {
        this.$refs[name].resetFields();
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            activityPlanAdd(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("活动计划填报成功");
                this.modal = false;
                this.init();
              } else {
                this.$Message.error('活动计划填报失败!');
              }
            });
          }
        })
      },
      editSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            activityPlanEdit(this.editForm).then(res => {
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
      },
      cancel() {
        this.$refs.form.resetFields();
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
