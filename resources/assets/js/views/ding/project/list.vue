<template>
    <div class="mui-content"> 
			<ul class="mui-table-view mui-table-view-striped mui-table-view-condensed">
        {{con_str}}
				<!-- <li class="mui-table-view-cell">
					<div class="mui-table">
						<div class="mui-table-cell mui-col-xs-10">
							<h4 class="mui-ellipsis">信息化推进办公室张彦合同付款信息化</h4>
							<h5>申请人：李四</h5>
							<p class="mui-h6 mui-ellipsis">Hi，李明明，申请交行信息卡，100元等你拿，李明明，申请交行信息卡，100元等你拿，</p>
						</div>
						<div class="mui-table-cell mui-col-xs-2 mui-text-right">
							<span class="mui-h5">12:25</span>
						</div>
					</div>
				</li>
				<li class="mui-table-view-cell">
					<div class="mui-table">
						<div class="mui-table-cell mui-col-xs-10">
							<h4 class="mui-ellipsis-2">信息化推进办公室张彦合同付款信息化推进办公室张彦合同付款信息化推进办公室张彦合同付款</h4>
							<h5>申请人：李四</h5>
							<p class="mui-h6 mui-ellipsis">Hi，李明明，申请交行信息卡，100元等你拿，李明明，申请交行信息卡，100元等你拿，</p>
						</div>
						<div class="mui-table-cell mui-col-xs-2 mui-text-right">
							<span class="mui-h5">12:25</span>
						</div>
					</div>
				</li> -->
			</ul>
    </div>
    <nav class="mui-bar mui-bar-tab">
      <a class="mui-tab-item mui-active" href="/ding/project">
        <span class="mui-icon mui-icon-home"></span>
        <span class="mui-tab-label">应用</span>
      </a>
      <a class="mui-tab-item" href="#">
        <span class="mui-icon mui-icon-email"><span class="mui-badge">9</span></span>
        <span class="mui-tab-label">消息</span>
      </a>
    </nav>
</template>
<style scope src="./index.css"></style>
<style scope src="./mui.css"></style>
<script>
import * as dd from "dingtalk-jsapi";
import { getAllProjects } from "../../../api/ding";
export default {
  data() {
    return {
      data:[],
      con_str:''
    };
  },
  mounted() {
    this.getProject();
  },
  methods: {
    init() {
      // this.getProject();
    },
    getProject() {
      if(sessionStorage.getItem('userid')==''){
        this.$Message.error("请重新获取用户信息");
        return false;
      }
      getAllProjects({userid:sessionStorage.getItem('userid')}).then(e => {
          if(e.result){
            let str='';
            e.result.forEach(function (row, index) {
              str += '<li class="mui-table-view-cell">'+
                    '<div class="mui-table">'+
                      '<div class="mui-table-cell mui-col-xs-10">'+
                        '<h4 class="mui-ellipsis">'+row.title+'</h4>'+
                        '<h5>'+row.status+'</h5>'+
                        '<p class="mui-h6 mui-ellipsis">'+row.type+'</p>'+
                      '</div>'+
                      '<div class="mui-table-cell mui-col-xs-2 mui-text-right">'+
                        '<span class="mui-h5">'+row.created_at+'</span>'+
                      '</div>'+
                    '</div>'+
                  '</li>';
            })
            alert(JSON.stringify(e))
            this.con_str=str;
          }else{
            this.$Message.error("无项目信息");
          }
      })
    }
  }
};
</script>
<style scope>
</style>

