<template>
  <div class="mui-content">
    <div class="mui-content"> 
			<ul class="mui-table-view mui-table-view-striped mui-table-view-condensed">
				<li class="mui-table-view-cell">
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
				</li>
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
      data:[]
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
      alert(sessionStorage.getItem('user_id'))
      getAllProjects({userid:sessionStorage.getItem('user_id')}).then(e => {
          alert(444)
      })
    }
    // getProject() {
    //   // this.tableLoading = true;
    //   // this.searchForm.is_audit = this.$route.params.is_audit;
    //   getAllProjects({userid:sessionStorage.getItem('user_id')}).then(e => {
    //     this.data = e.result;
    //     // //分页显示所有数据总数
    //     // this.dataCount = this.data.length;
    //     // //循环展示页面刚加载时需要的数据条数
    //     // this.nowData = [];
    //     // for (let i = 0; i < this.pageSize; i++) {
    //     //   if (this.data[i]) {
    //     //     this.nowData.push(this.data[i]);
    //     //   }
    //     // }
    //     // this.pageCurrent = 1;

    //     // if (this.searchForm.title || this.searchForm.subject || this.searchForm.office || this.searchForm.unit || this.searchForm.num || this.searchForm.type || this.searchForm.build_type || this.searchForm.money_from || this.searchForm.is_gc || this.searchForm.nep_type || this.searchForm.status) {
    //     //   this.exportBtnDisable = false;
    //     // }
    //     // this.tableLoading = false;
    //   });
    // }
  }
};
</script>
<style scope>
</style>

