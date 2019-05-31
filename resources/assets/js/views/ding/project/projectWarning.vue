<template>
    <div class="mui-content"> 
      <div class="mui-content" style="margin-bottom:30px"> 
        <ul class="mui-table-view mui-table-view-striped mui-table-view-condensed" v-html="con_str">
        </ul>
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
import { getAllWarning } from "../../../api/ding";
export default {
  data() {
    return {
      data:[],
      con_str:''
    };
  },
  mounted() {
    this.getProjectWarning();
  },
  methods: {
    init() {
    },
    getProjectWarning() {
      if(sessionStorage.getItem('userid')==''){
        this.$Message.error("请重新获取用户信息");
        return false;
      }
      getAllWarning({userid:sessionStorage.getItem('userid')}).then(res => {
        alert(JSON.stringify(res))
        if(res.result){
          let str='';
          let war_title='';
          e.result.forEach(function (row, index) {
            if (row.tags === 1) {
            war_title = '警告滞后';
          } else if (row.tags === 2) {
            war_title = '严重滞后';
          }
            str += '<li class="mui-table-view-cell">'+
                  '<div class="mui-table">'+
                    '<div class="mui-table-cell mui-col-xs-10">'+
                      '<h4 class="mui-ellipsis">'+row.title+'</h4>'+
                      '<h5>'+war_title+'</h5>'+
                    '</div>'+
                    '<div class="mui-table-cell mui-col-xs-2 mui-text-right">'+
                      '<span class="mui-h5">'+row.schedule_at+'</span>'+
                    '</div>'+
                  '</div>'+
                '</li>';
          })
          this.con_str=str;
        }else{
          this.$Message.error("无项目进度信息");
        }
      });
    }
  }
};
</script>
<style scope>
</style>

