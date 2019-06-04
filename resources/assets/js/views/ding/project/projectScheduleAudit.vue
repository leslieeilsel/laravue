<template>
  <div class="mui-content">
    <ul class="mui-table-view" style="padding:10px 15px;background:transparent;top:0;margin-bottom: 30px;">
      <li class="ding_li">
        <div class="li_top">
          <div>项目审核</div>
        </div>
        <div class="ding_detail ding_details">
          <span class="ding_details_span">
            <font class="details_name">填报项目</font>
            <font class="details_det ding">
              <Input v-model="form.project_title" placeholder="项目名称" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">填报时间</font>
            <font class="details_det ding">
              <DatePicker type="month" placeholder="请选择"
                          format="yyyy-MM"
                          v-model="form.month" style="width: 90%"></DatePicker>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name" onClick='changeProject()'>项目编号</font>
            <font class="details_det ding">
                <Input v-model="form.project_num" placeholder="项目编号" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">投资主体</font>
            <font class="details_det ding">
              <Input v-model="form.subject" placeholder="投资主体" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">建设起止年限</font>
            <font class="details_det ding">
              <Row>
                <Col span="11">
                  <DatePicker type="month" placeholder="开始时间" format="yyyy-MM" readonly
                              v-model="form.build_start_at" style="width: 90%"></DatePicker>
                </Col>
                <Col span="2" style="text-align: center">-</Col>
                <Col span="11">
                  <DatePicker type="month" placeholder="结束时间" format="yyyy-MM" readonly
                              v-model="form.build_end_at" style="width: 90%"></DatePicker>
                </Col>
              </Row>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">总投资(万元)</font>
            <font class="details_det ding">
              <Input v-model="form.total_investors" placeholder="" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">年计划投资(万元)</font>
            <font class="details_det ding">
              <Input v-model="form.plan_investors" placeholder="" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">年形象进度</font>
            <font class="details_det ding">
              <Input type="textarea" :rows="2" v-model="form.plan_img_progress" style="width: 90%" placeholder="请输入..." readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">月实际完成投资(万元)</font>
            <font class="details_det ding">
              <InputNumber :min="0" :step="1.2" style="width: 90%;border:none;" v-model="form.month_act_complete"
                           placeholder="必填项"></InputNumber>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">累计完成投资(万元)</font>
            <font class="details_det ding">
              <Input v-model="form.acc_complete" style="width: 90%" placeholder="" readonly/>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">月形象进度</font>
            <font class="details_det ding">
              <Input type="textarea" :rows="3" v-model="form.month_img_progress" style="width: 90%" placeholder="请输入..."/>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">累计形象进度</font>
            <font class="details_det ding">
              <Input type="textarea" :rows="3" v-model="form.acc_img_progress" style="width: 90%" placeholder="请输入..."/>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">计划开工时间</font>
            <font class="details_det ding">
              <DatePicker type="month" placeholder="请选择" format="yyyy-MM"
                          v-model="form.plan_build_start_at" style="width: 90%" readonly></DatePicker>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">土地征收情况及前期手续办理情况</font>
            <font class="details_det ding">
              <Input v-model="form.exp_preforma" type="textarea" :rows="3" style="width: 90%" placeholder="请输入..."></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">存在问题</font>
            <font class="details_det ding">
              <Input v-model="form.problem" type="textarea" :rows="3" style="width: 90%" placeholder="请输入..."></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">整改措施</font>
            <font class="details_det ding">
              <Input v-model="form.measures" type="textarea" :rows="3" style="width: 90%" placeholder="请输入..."></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">备注</font>
            <font class="details_det ding">
              <Input v-model="form.marker" type="textarea" :rows="3" style="width: 90%" placeholder="请输入..."></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">形象进度</font>
            <font class="details_det ding">
              <div class="demo-upload-list" v-for="item in defaultList">
                <template>
                  <img :src="item.url">
                  <div class="demo-upload-list-cover">
                    <Icon type="ios-eye-outline" @click.native="handleView(item.url)"></Icon>
                  </div>
                </template>
                <template>
                  <Progress hide-info></Progress>
                </template>
              </div>
              <!-- <Modal title="查看照片" v-model="visible">
                <img :src="imgUrl" style="width: 100%">
              </Modal> -->
              <!-- <Upload
                ref="upload"
                :disabled="upbtnDisabled"
                name="img_pic"
                :on-success="handleSuccess"
                multiple
                :data="upData"
                :format="['jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'pdf']"
                :max-size="600"
                :on-format-error="handleFormatError"
                :on-exceeded-size="handleMaxSize"
                action="/api/project/uploadPic">
                <Button icon="ios-cloud-upload-outline">上传</Button>
                <div style="color:#ea856b">文件大小不能超过600KB,请确保上传完毕之后再提交保存</div>
              </Upload> -->
            </font>
          </span>
        </div>
      </li>
      <Button @click="submitF()" style="width: 100%;height: 40px;background: #029aed; color:#fff; bottom:30px">审核</Button>
    </ul>
    <nav class="mui-bar mui-bar-tab">
      <a class="mui-tab-item mui-active" href="/ding/project">
        <span class="mui-icon mui-icon-home"></span>
        <span class="mui-tab-label">应用</span>
      </a>
      <a class="mui-tab-item" href="#">
        <span class="mui-icon mui-icon-email"><span class="mui-badge">1</span></span>
        <span class="mui-tab-label">消息</span>
      </a>
    </nav>
  </div>
</template>
<style scope src="./index.css"></style>
<style scope src="./mui.css"></style>
<script>
import * as dd from "dingtalk-jsapi";
import { projectPlanInfo,actCompleteMoney,getAuditedProjects, getUserId, userNotify,projectProgress,projectScheduleInfo,auditProjectProgress } from "../../../api/ding";
export default {
  data() {
    return {
      form: {
        month: "",
        project_id: "",
        project_title: "",
        project_num: "",
        subject: "",
        build_start_at: "",
        build_end_at: "",
        total_investors: "",
        plan_investors: "",
        plan_img_progress: "",
        month_act_complete: null,
        month_img_progress: "",
        acc_complete: "",
        acc_img_progress: "",
        problem: "",
        plan_build_start_at: "",
        exp_preforma: "",
        img_progress_pic: "",
        marker: "",
        is_audit: ""
      },
      project_id: [],
      userid: "",
      upData: {},
      defaultList: []
    };
  },
  mounted() {
    this.init();
  },
  methods: {
    init() {
      this.getScheduleInfo();
    },//进度详情
    getScheduleInfo(){
      let id=this.$route.query.id;
      this.is_loading(1);
      projectScheduleInfo({userid:sessionStorage.getItem('userid'),id:id}).then(res => {
        this.is_loading(0);
        this.form = res.result;
      })
    },
    submitF(){
      let id=this.$route.query.id;
      
      // dd.ready(function() {
      //   dd.device.notification.confirm({
      //       message: "审核",
      //       title: "是否通过审核",
      //       buttonLabels: ['通过', '不通过'],
      //       onSuccess : function(result) {
      //         auditProjectProgress({userid:sessionStorage.getItem('userid'),id:id,status:1,reason:''}).then(res => {
      //           alert(211)
      //           // this.is_loading(0);
      //           alert(JSON.stringify(res))
      //         })
      //       },
      //       onFail : function(err) {}
      //   });
      // })
      dd.device.notification.actionSheet({
          title: "审核", //标题
          cancelButton: '取消', //取消按钮文本
          otherButtons: ["审核通过","审核不通过"],
          onSuccess : function(result) {
            //审核通过1 审核不通过2
            if(result.buttonIndex==0){
              //通过
              alert(id)
              // this.is_loading(1);
              auditProjectProgress({userid:sessionStorage.getItem('userid'),id:id,status:1,reason:''}).then(res => {
                alert(211)
                // this.is_loading(0);
                alert(JSON.stringify(res))
              })
            }else{
              //不通过
              dd.device.notification.prompt({
                  message: "审核不通过原因",
                  title: "审核不通过原因",
                  defaultText:"默认提示",
                  buttonLabels: ['提交'],
                  onSuccess : function(result) {
                    if(result.buttonIndex==0){
                      this.is_loading(1);
                      auditProjectProgress({userid:sessionStorage.getItem('userid'),id:id,status:2,reason:result.value}).then(res => {
                        this.is_loading(0);
                        alert(JSON.stringify(res))
                      })
                    }
                  },
                  onFail : function(err) {}
              });
            }
            
          },
          onFail : function(err) {}
      })
    },
    //加载样式
    is_loading(type){
      if(type==1){
        dd.device.notification.showPreloader({
            text: "使劲加载中..", //loading显示的字符，空表示不显示文字
            showIcon: true, //是否显示icon，默认true
            onSuccess : function(result) {},
            onFail : function(err) {}
        })
      }else{
        dd.device.notification.hidePreloader({
            onSuccess : function(result) {},
            onFail : function(err) {}
        })
      }
    }
  }
};
</script>
<style scope>
.ivu-select-selection{
  border:none !important;
}
.ivu-select-placeholder{
  padding-left: 16px !important;
}
.ivu-input-prefix, .ivu-input-suffix{
  top: -5px;
}
</style>

