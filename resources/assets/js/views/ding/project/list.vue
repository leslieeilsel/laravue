<template>
    <div class="mui_content">
        <ul class="mui-table-view" style="margin:10px 15px;border-radius: 8px;">
            <li class="mui-table-view-cell mui-media">
                <a href="javascript:;">
                    <div class="mui-media-body">
                        幸福
                        <p class='mui-ellipsis'>能和心爱的人一起睡觉，是件幸福的事情；可是，打呼噜怎么办？</p>
                    </div>
                </a>
            </li>
            <li class="mui-table-view-cell mui-media">
                <a href="javascript:;">
                    <div class="mui-media-body">
                        木屋
                        <p class='mui-ellipsis'>想要这样一间小木屋，夏天挫冰吃瓜，冬天围炉取暖.</p>
                    </div>
                </a>
            </li>
            <li class="mui-table-view-cell mui-media">
                <a href="javascript:;">
                    <div class="mui-media-body">
                        CBD
                        <p class='mui-ellipsis'>烤炉模式的城，到黄昏，如同打翻的调色盘一般.</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</template>
<script>
import * as dd from 'dingtalk-jsapi'
  import {
    getAuditedProjects,
    projectProgress,
    projectProgressList,                                                                                                              projectPlanInfo,
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
  // import './index.css';
  // import './mui.css';
  export default {
    data() {
      return {
		    project_list:[]
	    }
    },
    mounted() {
	  this.init();
	 	// dd.biz.util.datepicker({
		// 	format: 'yyyy-MM-dd',//注意：format只支持android系统规范，即2015-03-31格式为yyyy-MM-dd
		// 	value: '2015-04-17', //默认显示日期
		// 	onSuccess : function(result) {
		// 		//onSuccess将在点击完成之后回调
		// 		/*{
		// 			value: "2015-02-10"
		// 		}
		// 		*/
		// 	},
		// 	onFail : function(err) {}
		// })
    },
    methods: {
      init() {
        this.getProjectId();
      },
      getProjectId() {
        getAuditedProjects().then(res => {
          this.project_id = res.result;
        });
      },
	  aaa(){
			dd.ready(function() {
				dd.device.base.getPhoneInfo({
					onSuccess : function(data) {
						alert(json.stringify(data));
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
			})
	  }
	  
    }
  }
</script>