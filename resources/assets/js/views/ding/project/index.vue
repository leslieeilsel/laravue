<template>
	<div class="mui-content">
		<ul class="mui-table-view" style="padding:10px 15px;background:transparent;">
			<li class="ding_li">
				<div class="li_top">
					<div>项目填报</div>
				</div>
				<div class="ding_detail ding_details">
					<span class="ding_details_span">
						<font class="details_name">填报项目</font>
						<font class="details_det ding">
							<select class="mui-input-select" id="project_id" @on-change="changeProject">
								<Option v-for="item in project_id" :value="item.id" :key="item.id">{{ item.title }}</Option>
							</select>
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">填报时间</font>
						<font class="details_det ding">
							<input type="month" class="mui-input-text" name="month" value='' placeholder="填报时间">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">项目编号</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="project_num" value='' placeholder="项目编号">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">投资主体</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="subject" value='' placeholder="投资主体">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">建设起止年限</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="build_at" value='' placeholder="建设起止年限">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">总投资(万元)</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="total_investors" value='' placeholder="总投资(万元)">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">年计划投资(万元)</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="plan_investors" value='' placeholder="年计划投资">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">年形象进度</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="plan_img_progress" value='' placeholder="年形象进度">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">月实际完成投资(万元)</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="month_act_complete" value='' placeholder="从开始至今实际完成投资">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">累计完成投资(万元)</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="acc_complete" value='累计完成投资' placeholder="">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">计划开工时间</font>
						<font class="details_det ding">
							<input type="date" class="mui-input-text" name="plan_build_start_at" value='计划开工时间' placeholder="">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">土地征收情况及前期手续办理情况</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="exp_preforma" value='土地征收情况及前期手续办理情况' placeholder="">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">存在问题</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="problem" value='存在问题' placeholder="">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">备注</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="marker" value='备注' placeholder="">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">形象进度</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" name="img_progress_pic" value='备注' placeholder="">
						</font>
					</span>
				</div>
			</li>
		</ul>
	</div>
</template>
<style scope src="./index.css"></style>
<style scope src="./mui.css"></style>
<script>
import * as dd from 'dingtalk-jsapi'
  import {
    getAuditedProjects,
    projectProgress,
    projectProgressList,                                                                                    
    projectPlanInfo,
    editProjectProgress,
    getData,
    auditProjectProgress,
    toAuditSchedule,
    actCompleteMoney,
    getProjectNoScheduleList,
    projectScheduleMonth,
    getProjectDictData,
		projectScheduleDelete
  } from '../../../api/project';
  import {
		getUserNotify
  } from '../../../api/ding';
  export default {
    data() {
      return {
        form: {
          month: '',
          project_id: '',
          project_num: '',
          subject: '',
          build_start_at: '',
          build_end_at: '',
          total_investors: '',
          plan_investors: '',
          plan_img_progress: '',
          month_act_complete: null,
          month_img_progress: '',
          acc_complete: '',
          acc_img_progress: '',
          problem: '',
          plan_build_start_at: '',
          exp_preforma: '',
          img_progress_pic: '',
          marker: '',
          is_audit: ''
				},
				project_id:[]
	  	}
    },
    mounted() {
	  	this.init();
    },
    methods: {
      init() {
        this.getProjectId();
        this.aaa();
      },
      getProjectId() {
        getAuditedProjects().then(res => {
          this.project_id = res.result;
        });
      },
      changeProject(e) {
        this.project_id.forEach((em) => {
          if (em.id === e) {
            this.form.subject = em.subject;
            this.form.project_num = em.num;
            this.form.build_start_at = em.plan_start_at;
            this.form.build_end_at = em.plan_end_at;
            this.form.total_investors = em.amount;
            this.form.plan_img_progress = em.image_progress;
            this.form.plan_build_start_at = em.plan_start_at;
          }
        });
	  },
	  aaa(){
			dd.ready(function() {
				// getUserNotify().then(res => {
        //   alert(JSON.stringify(res))
					
        // });
				dd.runtime.permission.requestAuthCode({
						corpId: "dinge48f324dae7de1df35c2f4657eb6378f",
						onSuccess: function(result) {
							alert(JSON.stringify(result))
						/*{
								code: 'hYLK98jkf0m' //string authCode
						}*/
						},
						onFail : function(err) {}
				
				})
				// dd.device.notification.alert({
				// 		message: "亲爱的",
				// 		title: "提示",//可传空
				// 		buttonName: "收到",
				// 		onSuccess : function() {
				// 			console.log(111);
				// 				//onSuccess将在点击button之后回调
				// 				/*回调*/
				// 		},
				// 		onFail : function(err) {}
				// });
			// 	dd.config({
			// 		agentId: '252426258', // 必填，微应用ID
			// 		corpId: 'dinge48f324dae7de1df35c2f4657eb6378f',//必填，企业ID
			// 		timeStamp: '', // 必填，生成签名的时间戳
			// 		nonceStr: '$10$q7IuhSqsnGL5g3CNQEypleEuDMZrJyQImZqwSlLEORMoGHBp9u9.u', // 必填，生成签名的随机串
			// 		signature: '', // 必填，签名
			// 		type:0/1,   //选填，0表示微应用的jsapi，1表示服务窗的jsapi，不填默认为0。该参数从dingtalk.js的0.8.3版本开始支持
			// 		jsApiList:[
			// 				'biz.contact.choose',
			// 		],
			// });
				// dd.runtime.permission.requestAuthCode({
				// 		corpId: "dinge48f324dae7de1df35c2f4657eb6378f",
				// 		onSuccess: function(result) {
				// 			console.log(result);
							
				// 		/*{
				// 				code: 'hYLK98jkf0m' //string authCode
				// 		}*/
				// 		},
				// 		onFail : function(err) {}
				
				// })
			})
	  }
	  
    }
  }
</script>
