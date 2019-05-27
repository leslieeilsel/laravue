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
							<select class="mui-input-select" id="project_id" v-model="form.project_id" filterable @on-change="changeProject">
								<option value="">请选择</option>
								<option v-for="item in project_id" :value="item.id" :key="item.id">{{ item.title }}</option>
							</select>
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">填报时间</font>
						<font class="details_det ding">
							<input type="month" class="mui-input-text" v-model="form.month" value='' placeholder="填报时间">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">项目编号</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.project_num" value='' placeholder="项目编号">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">投资主体</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.subject" value='' placeholder="投资主体">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">建设起止年限</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.build_at" value='' placeholder="建设起止年限">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">总投资(万元)</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.total_investors" value='' placeholder="总投资(万元)">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">年计划投资(万元)</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.plan_investors" value='' placeholder="年计划投资">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">年形象进度</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.plan_img_progress" value='' placeholder="年形象进度">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">月实际完成投资(万元)</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.month_act_complete" value='' placeholder="从开始至今实际完成投资">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">累计完成投资(万元)</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.acc_complete" placeholder="累计完成投资">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">计划开工时间</font>
						<font class="details_det ding">
							<input type="date" class="mui-input-text" v-model="form.plan_build_start_at" placeholder="计划开工时间">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">土地征收情况及前期手续办理情况</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.exp_preforma" placeholder="土地征收情况及前期手续办理情况">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">存在问题</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.problem" placeholder="存在问题">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">备注</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.marker" placeholder="备注">
						</font>
					</span>
					<span class="ding_details_span">
						<font class="details_name">形象进度</font>
						<font class="details_det ding">
							<input type="text" class="mui-input-text" v-model="form.img_progress_pic" placeholder="形象进度">
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
		projectPlanInfo,
  } from '../../../api/project';
  import {
		getAuditedProjects,
		getUserId,
		userNotify
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
				project_id:[],
				userid:''
	  	}
    },
    mounted() {
	  	this.init();
    },
    methods: {
      init() {
				// this.aaa();
				this.getProjectId();
      },
      getProjectId() {
        getAuditedProjects().then(res => {					
					this.project_id = res.result;		
        });
      },
			changeProject(e) {
				alert(222)
				this.project_id.forEach((em) => {
					if (em.id === e) {
						this.form.subject = em.subject;
						this.form.project_num = em.num;
						this.form.build_start_at = em.plan_start_at;
						this.form.build_end_at = em.plan_end_at;
						this.form.total_investors = em.amount;
						this.form.plan_img_progress = em.image_progress;
						this.form.plan_build_start_at = em.plan_start_at;
						
						let month_time = new Date();
						let month_time_0 = (month_time.getMonth() + 1) > 9 ? (month_time.getMonth() + 1) : '0' + (month_time.getMonth() + 1);

						month_time = month_time.getFullYear() + '-' + month_time_0;
						projectPlanInfo({month: month_time, project_id: this.form.project_id}).then(res => {
							this.form.plan_investors = res.result.amount;
							this.form.plan_img_progress = res.result.image_progress;
						});
					}
				});
			},
			aaa(){
				dd.ready(function() {
					dd.runtime.permission.requestAuthCode({
							corpId: "dinge48f324dae7de1df35c2f4657eb6378f",
							onSuccess: function(result) {
								getUserId({code:result.code}).then(res => {
									// alert(JSON.stringify(res)+"$$$");
									if(res.errcode==0){			
										this.userid=res.userid;
										this.getProjectId();
									}
								});
							},
							onFail : function(err) {}
					})
				})
			}
		}
  }
</script>
