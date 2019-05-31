<template>
    <div class="mui-content"> 
      <div class="mui-content" style="margin-bottom:30px"> 
        <ul class="mui-table-view mui-table-view-striped mui-table-view-condensed">
          {{con_str}}
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
    </div>
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
          alert(JSON.stringify(e))
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

