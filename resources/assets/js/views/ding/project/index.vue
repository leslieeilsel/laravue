<template>
  <div class="mui-content">
    <div class="mui-content" :style='index_display'> 
        <ul class="mui-table-view mui-grid-view mui-grid-9" style="background: #fff;">
              <li class="mui-table-view-cell mui-media mui-col-xs-6 mui-col-sm-6" :class-name="projectScheduleDsp">
                <a href="/#/ding/project/projectSchedule">  
                      <span class="mui-icon mui-icon-compose"></span>  
                      <div class="mui-media-body">填报</div>
                </router-link>
              </li>  
            <li class="mui-table-view-cell mui-media mui-col-xs-6 mui-col-sm-6">
                <a href="/#/ding/project/list">  
                      <span class="mui-icon mui-icon-bars"></span>  
                      <div class="mui-media-body">项目</div>
                </a>
            </li>  
            <li class="mui-table-view-cell mui-media mui-col-xs-6 mui-col-sm-6">
                <a href="/#/ding/project/projectScheduleList">  
                      <span class="mui-icon mui-icon-settings"></span>  
                      <div class="mui-media-body">进度</div>
                </a>
            </li>  
            <li class="mui-table-view-cell mui-media mui-col-xs-6 mui-col-sm-6">
                <a href="/#/ding/project/projectWarning">  
                      <span class="mui-icon mui-icon-info"></span>  
                      <div class="mui-media-body">预警</div>
                </a>
            </li>  
        </ul>
    </div>
    <nav class="mui-bar mui-bar-tab">
      <a class="mui-tab-item mui-active" href="/#/ding/project">
        <span class="mui-icon mui-icon-home"></span>
        <span class="mui-tab-label">应用</span>
      </a>
      <a class="mui-tab-item" href="/#/ding/project/notify">
      <!-- <span class="mui-badge">1</span> -->
        <span class="mui-icon mui-icon-email"></span>
        <span class="mui-tab-label">消息</span>
      </a>
    </nav>
  </div> 
</template>
<style scope src="./index.css"></style>
<style scope src="./mui.css"></style>
<script>
import * as dd from "dingtalk-jsapi";
import { getUserId } from "../../../api/ding";
export default {
  data() {
    return {
      projectScheduleDsp:'dsp',
      listDsp:'dsp',
      projectScheduleListDsp:'dsp',
      projectWarningDsp:'dsp'
    };
  },
  mounted() {
    this.userInfo();
  },
  methods: {
    userInfo() {
      dd.ready(function() {
        dd.runtime.permission.requestAuthCode({
          corpId: "dinge48f324dae7de1df35c2f4657eb6378f",
          onSuccess: function(result) {
            dd.device.notification.showPreloader({
                text: "使劲加载中..", //loading显示的字符，空表示不显示文字
                showIcon: true, //是否显示icon，默认true
                onSuccess : function(result) {},
                onFail : function(err) {}
            })
            getUserId({ code: result.code }).then(res => {
              dd.device.notification.hidePreloader({
                  onSuccess : function(result) {},
                  onFail : function(err) {}
              })
              if (res.result.errcode == 0) { 
                sessionStorage.setItem('userid',res.result.userid);
              }
              if (res.ids) {
                this.projectScheduleDsp='dsp'
                this.listDsp='dsp'
                this.projectScheduleListDsp='dsp'
                this.projectWarningDsp='dsp'
              }else{
                this.projectScheduleDsp='dspn'
                this.listDsp='dspn'
                this.projectScheduleListDsp='dspn'
                this.projectWarningDsp='dspn'
              }
              
            });
          },
          onFail: function(err) {}
        });
      });
    }
  }
};
</script>
<style scope>
.dsp{
  display: block;
}
.dspn{
  display: none;
}
</style>

