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
import { getNotifyList } from "../../../api/ding";
export default {
  data() {
    return {
      data:[],
      con_str:''
    };
  },
  mounted() {
    this.getNotify();
  },
  methods: {
    init() {
    },
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

