<template>
    <div class="mui-content"> 
      <div class="mui-content" style="margin-bottom:30px"> 
        <ul class="mui-table-view mui-table-view-striped mui-table-view-condensed" v-html="con_str">
          <!--  v-html="con_str" -->
          

          <!-- <li class="mui-table-view-cell">
            <div class="mui-row">
              <div class="mui-col-xs-12">
                <h4 class="mui-ellipsis">sdfsff</h4>
                <h5>sdfsdfsfdsf</h5>
                <p class="mui-h6 mui-ellipsis">dsfsdfdsfs</p>
              </div>
              <div class="mui-col-xs-4"><span class="mui-h5">2019</span></div>
              <div class="mui-col-xs-8" style="padding: 5px;">
                  <Button style="width: 50%;height: 30px;background: #029aed; color:#fff;float:left">查看</Button>
                  <Button style="width: 50%;height: 30px;background: #029aed; color:#fff;float:left ">编辑</Button>
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
import { projectProgressList } from "../../../api/ding";
export default {
  data() {
    return {
      data:[],
      con_str:''
    };
  },
  mounted() {
    this.getProjectSchedule();
  },
  methods: {
    init() {
    },
    getProjectSchedule() {
      if(sessionStorage.getItem('userid')==''){
        this.$Message.error("请重新获取用户信息");
        return false;
      }
      this.is_loading(1);
      projectProgressList({userid:sessionStorage.getItem('userid')}).then(res => {
        this.is_loading(0);
        if(res.result){
            let str='';
            res.result.forEach(function (row, index) {
              str += '<li class="mui-table-view-cell">'+
                        '<div class="mui-row">'+
                          '<div class="mui-col-xs-12">'+
                            '<h4 class="mui-ellipsis">'+row.project_title+'</h4>'+
                            '<h5>'+row.month_img_progress+'</h5>'+
                            '<p class="mui-h6 mui-ellipsis">'+row.month_act_complete+'</p>'+
                          '</div>'+
                          '<div class="mui-col-xs-4" style="padding: 5px; border-top:#e1e1e1 solid 1px;"><span class="mui-h5">'+row.month+'</span></div>'+
                          '<div class="mui-col-xs-8" style="padding: 5px; border-top:#e1e1e1 solid 1px;">'+
                              '<Button style="width: 50%;height: 30px;background: #029aed; float:left">'+
                              '<a href="/#/ding/project/projectScheduleAudit?id='+row.id+'" style="color:#fff;">查看/审核</a></Button>'+
                              '<Button style="width: 50%;height: 30px;background: #029aed; color:#fff;float:left ">'+
                              '<a href="/#/ding/project/projectScheduleEdit?id='+row.id+'" style="color:#fff;">编辑</a></Button>'+
                          '</div>'+
                      '</li>';
            })
            this.con_str=str;
          }else{
            this.$Message.error("无项目进度信息");
          }
      })
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

