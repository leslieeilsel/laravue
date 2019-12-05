<template>
  <Card>
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
      v-model="editModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="修改活动计划">
      <Form ref="editForm" :model="editForm" :label-width="90" :rules="editFormValidate">
        <Row>
          <Col span="24">
            <Divider>活动计划信息</Divider>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="活动名称" prop="activity_plan_id">
              <Select v-model="editForm.activity_plan_id" style="width:200px" readonly>
                  <Option v-for="item in data" :value="item.id" :key="item.id" >{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="区域" prop="area">
              <Input v-model="editForm.area" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="活动计划开始时间" prop="plan_start_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="editForm.plan_start_time" readonly></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动计划结束时间" prop="plan_end_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="editForm.plan_end_time" readonly></DatePicker>
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
              <Input v-model="editForm.position" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动复盘" prop="remark">
              <Input v-model="editForm.remark" type="textarea" :rows="3" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24" style="margin-left: 20px;">
            <template>
              <div class="demo-upload-list" v-for="item in editDefaultList">
                <template>
                  <img :src="item.url">
                  <div class="demo-upload-list-cover">
                    <Icon type="ios-eye-outline" @click.native="handleView(item.url)"></Icon>
                    <Icon type="ios-trash-outline" @click.native="handleRemove(item.url)"></Icon>
                  </div>
                </template>
              </div>
              <Modal title="查看照片" v-model="visible">
                <img :src="imgUrl" v-if="visible" style="width: 100%">
              </Modal>
            </template>
            <Upload
              ref="upload"
              name="pic"
              :show-upload-list="false"
              :default-file-list="editDefaultList"
              :on-success="editHandleSuccess"
              :format="['jpg', 'jpeg', 'png']"
              :on-format-error="handleFormatError"
              :before-upload="handleBeforeUpload"
              multiple
              :data="upData"
              type="drag"
              action="/api/value/uploadPic"
              style="display: inline-block;width:58px;">
              <div style="width: 58px;height:58px;line-height: 58px;">
                <Icon type="ios-camera" size="20"></Icon>
              </div>
            </Upload>
          </Col>
        </Row>
      </Form>
      <div slot="footer">
        <Button @click="handleReset('editForm')" :loading="loading">重置</Button>
        <Button
          @click="submitF('editForm')"
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
    activityImplementEdit,
    activityImplementDel,
    activityImplement,
    activityPlan,
    areaDepartmentList,
    getAreaShop 
  } from '../../../api/value';
  import './activityImplement.css'

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
            title: '活动位置',
            key: 'position',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '活动复盘',
            key: 'remark',
            // fixed: 'left',
            width: 320,
          },
          {
            title: '负责人',
            key: 'applicant',
            width: 220,
            align: "center"
          },
          {
            title: '活动执行时间',
            key: 'date_time',
            width: 220,
            align: "left"
          },
          {
            title: '操作',
            key: 'action',
            width: 280,
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
                      this.editForm.id=params.row.id;
                      this.editForm.activity_plan_id=params.row.activity_plan_id;
                      this.editForm.area=params.row.area;
                      this.editForm.plan_end_time=params.row.plan_end_time;
                      this.editForm.plan_start_time=params.row.plan_start_time;
                      this.editForm.position=params.row.position;
                      this.editForm.remark=params.row.remark;
                      this.editForm.pic=params.row.pic;
                      this.upData = {title: params.row.title};
                      let edit_img_pic = [];
                      if (params.row.pic) {
                        let edit_pics = params.row.pic.split(",");
                        edit_pics.forEach(function (item) {
                          let files = item.split("/");
                          let fileName = files[files.length - 1];
                          if (fileName !== 'null') {
                            edit_img_pic.push({url: item.replace(/#/g, "%23"), name: fileName});
                          }
                        })
                      }
                      this.editDefaultList = edit_img_pic;

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
                          activityImplementDel({id: params.row.id}).then(res => {
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
          pageSize: 10, // 页面大小,
          plan_id:0
        },
        submitLoading: false,
        upbtnPicDisabled:true,
        loading:true,
        modal: false,
        editModal:false,
        areaShop:[],
        editForm: {
          activity_plan_id:0,
          pic:'',
          remark:''
        },
        editFormValidate: {
          // 表单验证规则
          remark: [
            {required: true, message: "请填写活动复盘"}
          ],
          pic: [
            {required: true, message: "请上传图片"}
          ]
        },
        editDefaultList: [],
        visible: false,
        upData: {},
        imgUrl: '',
        uploadList:[]
      }
    },
    methods: {
      init() {
        console.log(2222);
        
        this.getActivityImplement();
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
      },//获取执行列表
      getActivityImplement() {
        this.searchForm.plan_id=this.$route.params.plan_id;
        console.log(222);
        
        this.tableLoading = true;
        activityImplement(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          // this.searchForm.pageNumber = 1;
          // this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getActivityImplement();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getActivityImplement();
      },
      handleReset(name) {
        this.$refs[name].resetFields();
        this.$refs.upload.clearFiles();
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            activityImplementEdit(this.editForm).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("活动执行修改成功");
                this.editModal = false;
                this.init();
              } else {
                this.$Message.error('活动执行修改失败!');
              }
            });
          }
        })
      },
      cancel() {
        this.$refs.editForm.resetFields();
        this.handleClearFiles();
      },
      handleClearFiles() {
        this.$refs.upload.clearFiles();
      },
      editHandleSuccess(res, file) {
        let files = res.result.split("/");
        let fileName = files[files.length - 1];
        if (fileName !== 'null') {
          this.editDefaultList.push({url: res.result, name: fileName});
        }
        if (this.editForm.pic) {
          this.editForm.pic = this.editForm.pic + ',' + res.result;
        } else {
          this.editForm.pic = res.result;
        }
      },
      handleFormatError(file) {
        this.$Notice.warning({
          title: '文件格式不正确',
          desc: '文件格式不正确，请选择JPG或PNG'
        });
      },
      handleBeforeUpload() {
        const check = this.uploadList.length < 5;
        if (!check) {
          this.$Notice.warning({
            title: '最多可上载5张图片'
          });
        }
        return check;
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
      },//上传图片
      handleView(url) {
        this.imgUrl = url;
        this.visible = true;
      },
      handleRemove(file) {
        const fileList = this.editDefaultList;
        fileList.forEach(function (fileObj, index) {
          if (file === fileObj.url) {
            fileList.splice(index, 1);
          }
        });
        if (fileList.length > 0) {
          let fileUrl = [];
          fileList.forEach(function (obj) {
            fileUrl.push(obj.url);
          });
          this.editForm.pic = fileUrl.join(',');
        } else {
          this.editForm.pic = '';
        }
      }
    },activated(){
      this.init();
    },
    mounted() {
      // this.init();
    }
  }
</script>
<style scoped>
  .ivu-divider-horizontal {
    margin: 0 0 24px 0 !important;
  }
</style>
