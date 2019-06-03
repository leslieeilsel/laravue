<template>
  <div class="mui-content">
    <ul class="mui-table-view" style="padding:10px 15px;background:transparent;top:0;margin-bottom: 30px;">
      <li class="ding_li">
        <div class="li_top">
          <div>项目填报</div>
        </div>
        <div class="ding_detail ding_details">
          <span class="ding_details_span">
            <font class="details_name">项目名称</font>
            <font class="details_det ding">
              <Input v-model="form.project_title" placeholder="项目名称" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name" onClick='changeProject()'>项目编号</font>
            <font class="details_det ding">
                <Input v-model="form.project_num" placeholder="项目编号" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">项目类型</font>
            <font class="details_det ding">
                <Input v-model="form.type" placeholder="项目类型" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">投资主体</font>
            <font class="details_det ding">
              <Input v-model="form.subject" placeholder="投资主体" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">建设性质</font>
            <font class="details_det ding">
              <Input v-model="form.build_type" placeholder="建设性质" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">承建单位</font>
            <font class="details_det ding">
              <Input v-model="form.unit_title" placeholder="承建单位" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">资金来源</font>
            <font class="details_det ding">
              <Input v-model="form.money_from" placeholder="资金来源" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">项目金额(万元)</font>
            <font class="details_det ding">
              <Input v-model="form.amount" placeholder="项目金额(万元)" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">土地费用(万元)</font>
            <font class="details_det ding">
              <Input v-model="form.land_amount" placeholder="土地费用(万元)" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">项目状态</font>
            <font class="details_det ding">
              <Input v-model="form.status" placeholder="项目状态" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">项目标识(是否为国民经济计划)</font>
            <font class="details_det ding">
              <Input v-model="form.is_gc" placeholder="项目标识(是否为国民经济计划)" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">国民经济计划分类</font>
            <font class="details_det ding">
              <Input v-model="form.nep_type" placeholder="国民经济计划分类" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">计划开始时间</font>
            <font class="details_det ding">
              <Input v-model="form.plan_start_at" placeholder="计划开始时间" style="width: 90%" readonly></Input>
            </font>
          </span>
          <span class="ding_details_span">
            <font class="details_name">计划结束时间</font>
            <font class="details_det ding">
              <Input v-model="form.plan_end_at" placeholder="计划结束时间" style="width: 90%" readonly></Input>
            </font>
          </span>
        </div>
      </li>
    </ul>
    <div slot="footer">
      <Button @click="submitF()" style="width: 100%;height: 40px;top: -5;background: #029aed; color:#fff;position: fixed;bottom: 0;">提交</Button>
    </div>
  </div>
</template>
<style scope src="./index.css"></style>
<style scope src="./mui.css"></style>
<script>
import * as dd from "dingtalk-jsapi";
import { getProjectInfo } from "../../../api/ding";
export default {
  data() {
    return {
      form: {
        title: '',
        num: '',
        subject: '',
        type: '',
        build_type: '',
        money_from: '',
        status: '',
        unit_title: '',
        unit: '',
        amount: null,
        land_amount: null,
        is_gc: '',
        nep_type: '',
        plan_start_at: '',
        plan_end_at: '',
        center_point: {},
        description: '',
      }
    };
  },
  mounted() {
    this.init();
    alert(this.$route.query.id)
  },
  methods: {
    init() {
      this.getProject();
    },
    getProject() {
      let id=this.$route.query.id;
      alert(id+'***')
      this.is_loading(1);
      getProjectInfo({id:id}).then(res => {
      this.is_loading(0);
        this.form = res.result;
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
.ivu-select-selection{
  border:none !important;
}
.ivu-select-placeholder{
  padding-left: 16px !important;
}
.ivu-input-prefix, .ivu-input-suffix{
  top: -5px;
}
</style>

