<template>
    <div class="mui-content"> 
      <ul class="mui-table-view mui-grid-view mui-grid-9" style="background: #fff; text-align: center;">
        <li class="mui-table-view-cell mui-media mui-col-xs-10 mui-col-sm-10">
          <a href="/#/ding/project/projectSchedule">  
            <div class="mui-media-body">未填报</div> 
            <div class="mui-media-body">{{noSchedule}}</div>
          </a>
        </li>  
        <li class="mui-table-view-cell mui-media mui-col-xs-12 mui-col-sm-12">
          <a href="/#/ding/project/projectSchedule">  
            <div class="mui-media-body">发送消息</div> 
          </a>
        </li>  
      </ul>
      <nav class="mui-bar mui-bar-tab">
        <a class="mui-tab-item mui-active" href="/#/ding/project">
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
import { getNotifyList } from "../../../api/ding";
export default {
  data() {
    return {
      data:[],
      con_str:'',
      noSchedule:0
    };
  },
  mounted() {
    this.getNotify();
  },
  methods: {
    getNotify() {
      if(sessionStorage.getItem('userid')==''){
        this.$Message.error("请重新获取用户信息");
        return false;
      }
      this.is_loading(1);
      getNotifyList({userid:sessionStorage.getItem('userid')}).then(res => {
        this.is_loading(0);
        if(res.result){
          let str='';
          let war_title='';
          res.result.forEach(function (row, index) {
            str += '<li class="mui-table-view-cell">'+
                  '<div class="mui-table">'+
                    '<div class="mui-table-cell mui-col-xs-10">'+
                      '<h4 class="mui-ellipsis">'+row.title+'</h4>'+
                      '<h5>'+row.send_user_name+'</h5>'+
                    '</div>'+
                    '<div class="mui-table-cell mui-col-xs-2 mui-text-right">'+
                      '<span class="mui-h5">'+row.description+'</span>'+
                    '</div>'+
                  '</div>'+
                '</li>';
          })
          this.con_str=str;
        }else{
          this.$Message.error("无消息");
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
</style>

