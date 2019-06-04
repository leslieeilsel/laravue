<template>
  <div class="mui-content">
    <ul class="mui-table-view" style="padding:10px 15px;background:transparent;top:0;margin-bottom: 30px;">
      <li class="ding_li">
        <div class="li_top">
          <div>项目填报</div>
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
              <DatePicker type="month" :options="month_options_0" placeholder="请选择"
                          format="yyyy-MM"
                          v-model="form.month" style="width: 90%" readonly></DatePicker>
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
                              v-model="form.build_start_at" style="width: 90%" readonly></DatePicker>
                </Col>
                <Col span="2" style="text-align: center">-</Col>
                <Col span="11">
                  <DatePicker type="month" placeholder="结束时间" format="yyyy-MM" readonly
                              v-model="form.build_end_at" style="width: 90%" readonly></DatePicker>
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
              <InputNumber @on-blur="changeMonthActComplete" :min="0" :step="1.2" style="width: 90%;border:none;" v-model="form.month_act_complete"
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
              <!-- <Upload 
                ref="upload"
                name="img_pic"
                :on-success="handleSuccess"
                action="/api/project/uploadPic"
              >
                <Button icon="ios-cloud-upload-outline">上传</Button>
                <div style="color:#ea856b">文件大小不能超过600KB,请确保上传完毕之后再提交保存</div>
              </Upload> -->
              <Upload
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
              </Upload>
            </font>
          </span>
        </div>
      </li>
    </ul>
    <div>
      <Button @click="submitF()" style="width: 100%;height: 40px;top: -5;background: #029aed; color:#fff;position: fixed;bottom: 0;">提交</Button>
    </div>
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
import { getAuditedProjects,projectPlanInfo,actCompleteMoney, getUserId, userNotify,projectProgress,projectScheduleInfo } from "../../../api/ding";
export default {
  data() {
    return {
      form: {
        month: "",
        project_id: "",
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
      upbtnDisabled: true,
      month_options_0: {
        disabledDate(date) {
          let date_at = new Date();
          let curr_time_0 = (date_at.getMonth() + 1) > 9 ? (date_at.getMonth() + 1) : '0' + (date_at.getMonth() + 1);
          let curr_time = date_at.getFullYear() + '-' + curr_time_0;

          let time_0 = (date.getMonth() + 1) > 9 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
          let time = date.getFullYear() + '-' + time_0;
          const disabledMonth = time;
          return disabledMonth !== curr_time;
          // return disabledMonth > curr_time;
        }
      }
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
        alert(JSON.stringify(res))
        
        let img_pic = [];
        if (res.result.img_progress_pic) {
          let pics = res.result.img_progress_pic.split(",");
          let i = 0;
          pics.forEach(function (em_pic) {
            if (em_pic != 'null') {
              img_pic.push({url: em_pic.replace(/#/g, "%23"), name: i});
            }
            i++;
          })
        }

        this.defaultList = img_pic;
        this.form = res.result;
      })
    },
    handleSuccess(res, file) {
      if (this.form.img_progress_pic) {
        this.form.img_progress_pic = this.form.img_progress_pic + ',' + res.result;
      } else {
        this.form.img_progress_pic = res.result;
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
    },// 月实际完成投资发生改变时 改变累计投资
    changeMonthActComplete(e) {
      if (this.form.project_id === '') {
        this.$Message.error('请先选择填报项目!');
        this.form.month_act_complete = null;
        return;
      }
      if (this.form.month === '') {
        this.$Message.error('请先选择填报时间!');
        this.form.month_act_complete = null;
        return;
      }
      actCompleteMoney({
        month: this.form.month,
        project_id: this.form.project_id,
        month_act_complete: this.form.month_act_complete,
        type: 'add'
      }).then(res => {
        this.form.acc_complete = res.result;
      });
    },
    submitF(){
      if(this.form.exp_preforma===''){
          this.$Message.error('请填写土地征收情况及前期手续办理情况!');
          return false;
      }
      if(this.form.month_act_complete===''){
          this.$Message.error('请填写月实际完成投资!');
          return false;
      }
      projectProgress({form:this.form,userid:sessionStorage.getItem('user_id')}).then(res => {
        if (res.result) {
          this.$Message.success("填报成功");
          this.modal = false;
          this.init();
        } else {
          this.$Message.error('填报失败!');
        }
      });
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

