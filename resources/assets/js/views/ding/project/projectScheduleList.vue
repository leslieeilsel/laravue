<template>
    <div class="mui-content"> 
      <div class="mui-content" style="margin-bottom:30px"> 
        <ul class="mui-table-view mui-table-view-striped mui-table-view-condensed">
          <!-- <li class="mui-table-view-cell">
            <div class="mui-table">
              <div class="mui-table-cell mui-col-xs-20">
                <h4 class="mui-ellipsis">sdfsff</h4>
                <h5>sdfsdfsfdsf</h5>
                <p class="mui-h6 mui-ellipsis">dsfsdfdsfs</p>
              </div>
              <div class="mui-table-cell mui-col-xs-4 mui-text-right">
                <span class="mui-h5">2019</span>
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
        alert(JSON.stringify(res.result))
        if(res.result){
          alert(22)
            let str='';
            res.result.forEach(function (row, index) {
              str += '<li class="mui-table-view-cell">'+
                        '<div class="mui-table">'+
                          '<div class="mui-table-cell mui-col-xs-20">'+
                            '<h4 class="mui-ellipsis">'+row.project_title+'</h4>'+
                            '<h5>'+row.month_img_progress+'</h5>'+
                            '<p class="mui-h6 mui-ellipsis">'+row.month_act_complete+'</p>'+
                          '</div>'+
                          '<div class="mui-table-cell mui-col-xs-4 mui-text-right">'+
                            '<span class="mui-h5">'+row.month+'</span>'+
                          '</div>'+
                        '</div>'+
                        '<div class="mui-table" style="height: 40px;line-height: 40px;border-top: #bbbaba solid 1px;font-size: 14px;">'+
                            '<Button style="width: 50%;height: 30px;background: #029aed; color:#fff">查看</Button>'+
                            '<Button style="width: 50%;height: 30px;background: #029aed; color:#fff ">编辑</Button>'+
                        '</div>'+
                      '</li>';
            })
            alert(JSON.stringify(str))
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

