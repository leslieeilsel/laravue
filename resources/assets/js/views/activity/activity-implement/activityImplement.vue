<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">活动执行填报</Button>
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
      title="活动执行填报">
      <Form ref="form" :model="form" :label-width="160">
        <Row>
          <Col span="24">
            <Divider>活动计划信息</Divider>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="活动名称" prop="activity_plan_id">
              <Select v-model="form.activity_plan_id" style="width:200px" @on-change="changePlanName">
                  <Option v-for="item in data" :value="item.id" :key="item.id" >{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="区域" prop="area">
              <Input v-model="form.area" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="活动计划开始时间" prop="plan_start_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.plan_start_time" readonly></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动计划结束时间" prop="plan_end_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.plan_end_time" readonly></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <Divider>活动执行信息</Divider>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="活动定位" prop="position">
              <Input v-model="form.position" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动复盘" prop="remark">
              <Input v-model="form.remark" type="textarea" :rows="3" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <FormItem label="图片" prop="pic">
            <Upload
              ref="upload"
              :disabled="upbtnPicDisabled"
              name="pic"
              :on-success="handleSuccessPic"
              :data="form"
              multiple
              :format="['jpg', 'jpeg', 'png']"
              :on-format-error="handleFormatError"
              :on-exceeded-size="handleMaxSize"
              action="/api/value/uploadPic">
              <Button icon="ios-cloud-upload-outline">上传</Button>
              <!-- <div style="color:#ea856b">文件大小不能超过600KB,请确保上传完毕之后再提交保存</div> -->
            </Upload>
          </FormItem>
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
  </Card>
</template>
<script>
  import {
    activityImplementAdd,
    activityImplement,
    activityPlan,
    areaDepartmentList,
    getAreaShop 
  } from '../../../api/value';
  import './activityImplement.css'
  import activityImplementList from '@/views/activity/activity-implement/activityImplementList.vue'
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
            title: '活动文字描述',
            key: 'remark',
            width: 300,
            align: "left"
          },
          {
            title: '活动状态',
            key: 'tags',
            width: 180,
            render: (h, params) => {
              let button_rbg = '';
              let activity_title = '';

              let plan_end_time = params.row.plan_end_time;
              let plan_start_time = params.row.plan_start_time;
              
              let day = new Date();
              day.setTime(day.getTime());
              let s2 = day.getFullYear()+"-" + (day.getMonth()+1) + "-" + day.getDate();
              if(s2>plan_end_time){
                button_rbg = 'warning';
                activity_title = '活动已经结束';
              }else if(s2<plan_start_time){
                button_rbg = 'error';
                activity_title = '活动还没开始';
              }else if(s2<=plan_end_time&&s2>=plan_start_time){
                button_rbg = 'success';
                activity_title = '活动进行中';
              } 
              return h("div", [
                h(
                  "Button",
                  {
                    props: {
                      type: button_rbg,
                      size: "small"
                    },
                    style: {
                      marginRight: "5px"
                    },
                  },
                  activity_title
                )
              ]);
            }
          },
          {
            title: '操作',
            key: 'tags',
            width: 180,
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
                      console.log(params);
                      this.$router.push({ 
                          path: '/activity-implement-list',
                          name:'activityImplementList',
                          component:  activityImplementList,
                          params:{id:params.row.id}})
                    }
                  }
                }, '查看执行列表')
              ])
            }
          }
        ],
        aI_columns: [
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
            key: 'name',
            width: 100,
            // fixed: 'left',
            align: "center"
          },
          {
            title: '活动位置',
            key: 'position',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '区域',
            key: 'area',
            width: 100,
            align: 'left'
          },
          {
            title: '负责人',
            key: 'applicant',
            width: 100,
            align: "center"
          },
          {
            title: '活动时间',
            key: 'date_time',
            width: 200,
            align: "left"
          },
          {
            title: '图片',
            key: 'pic',
            width: 200,
            align: "left"
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
        upbtnPicDisabled:true,
        loading:true,
        modal: false,
        areaShop:[],
        form: {
          name: '',
          position: '',
          area: '',
          applicant: '',
          date_time: '',
          pic: '',
          title:''
        }
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
          this.searchForm.pageNumber = 1;
          this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getSalesDataList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getSalesDataList();
      },
      handleReset(name) {
        this.$refs[name].resetFields();
        this.$refs.upload.clearFiles();
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            activityImplementAdd(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("活动执行填报成功");
                this.modal = false;
                this.init();
              } else {
                this.$Message.error('活动执行填报失败!');
              }
            });
          }
        })
      },
      cancel() {
        this.$refs.form.resetFields();
        this.handleClearFiles();
      },
      handleClearFiles() {
        this.$refs.upload.clearFiles();
      },
      handleSuccessPic(res, file) {
        if (this.form.pic) {
          this.form.pic = this.form.pic + ',' + res.result;
        } else {
          this.form.pic = res.result;
        }
      },
      handleFormatError(file) {
        this.$Notice.warning({
          title: '文件格式不正确',
          desc: '文件格式不正确，请选择JPG或PNG'
        });
      },
      handleMaxSize(file) {
        this.$Notice.warning({
          title: '超出文件大小限制',
          desc: '文件太大，不能超过600KB'
        });
      },
      changePlanName(e){
        let _this = this;
        this.upbtnPicDisabled=false;
        this.data.forEach(function(v){
          if(v.id==e){
            let area_id=JSON.parse(v.area_id);
            _this.form.title=v.title;
            _this.form.area=v.area;
            _this.form.plan_start_time=v.plan_start_time;
            _this.form.plan_end_time=v.plan_end_time;
          }
        })
      },
      handleSuccess(res, file) {
        if (this.form.pic) {
          this.form.pic = this.form.pic + ',' + res.result;
        } else {
          this.form.pic = res.result;
        }
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
