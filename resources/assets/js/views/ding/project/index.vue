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
<script>
dd.ready(function() {
    dd.device.base.getPhoneInfo({
    onSuccess : function(data) {
		console.log(data);
		
        // {
        //     screenWidth: 1080, // 手机屏幕宽度
        //     screenHeight: 1920, // 手机屏幕高度
        //     brand:'Mi', // 手机品牌
        //     model:'Note4', // 手机型号
        //     version:'7.0', // 版本
        //     netInfo:'wifi' , // 网络类型 wifi／4g／3g 
        //     operatorType:'xx' // 运营商信息
        // }
    },
    onFail : function(err) {}
});
});
//   import {
//     getAuditedProjects,
//     projectProgress,
//     projectProgressList,
//     projectPlanInfo,
//     editProjectProgress,
//     getData,
//     auditProjectProgress,
//     toAuditSchedule,
//     actCompleteMoney,
//     getProjectNoScheduleList,
//     projectScheduleMonth,
//     getProjectDictData,
//     projectScheduleDelete
//   } from '../../../api/project';
//   import './index.css';
//   import '../mui.css';
//   export default {
//     data() {
//       return {
//         form: {
//           month: '',
//           project_id: '',
//           project_num: '',
//           subject: '',
//           build_start_at: '',
//           build_end_at: '',
//           total_investors: '',
//           plan_investors: '',
//           plan_img_progress: '',
//           month_act_complete: null,
//           month_img_progress: '',
//           acc_complete: '',
//           acc_img_progress: '',
//           problem: '',
//           plan_build_start_at: '',
//           exp_preforma: '',
//           img_progress_pic: '',
//           marker: '',
//           is_audit: ''
// 		},
// 		project_id:[]
// 	  }
//     },
//     mounted() {
// 	  this.init();
// 	 	dd.biz.util.datepicker({
// 			format: 'yyyy-MM-dd',//注意：format只支持android系统规范，即2015-03-31格式为yyyy-MM-dd
// 			value: '2015-04-17', //默认显示日期
// 			onSuccess : function(result) {
// 				//onSuccess将在点击完成之后回调
// 				/*{
// 					value: "2015-02-10"
// 				}
// 				*/
// 			},
// 			onFail : function(err) {}
// 		})
//     },
//     methods: {
//       init() {
//         this.getProjectId();
//       },
//       getProjectId() {
//         getAuditedProjects().then(res => {
//           this.project_id = res.result;
//         });
//       },
//       changeProject(e) {
//         this.project_id.forEach((em) => {
//           if (em.id === e) {
//             this.form.subject = em.subject;
//             this.form.project_num = em.num;
//             this.form.build_start_at = em.plan_start_at;
//             this.form.build_end_at = em.plan_end_at;
//             this.form.total_investors = em.amount;
//             this.form.plan_img_progress = em.image_progress;
//             this.form.plan_build_start_at = em.plan_start_at;
//           }
//         });
//       }
//     }
//   }
</script>