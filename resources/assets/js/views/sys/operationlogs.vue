<template>
  <Card>
    <Table border stripe :columns="columns" :data="nowData" :loading="loading"></Table>
    <Row type="flex" justify="end" class="page">
      <Page :total="dataCount" :page-size="pageSize" @on-change="changePage" @on-page-size-change="_nowPageSize"
            show-total show-sizer/>
    </Row>
  </Card>
</template>
<script>
  import {getOperationLogs} from "../../api/log";

  export default {
    data() {
      return {
        pageSize: 10,   // 每页显示多少条
        dataCount: 0,   // 总条数
        pageCurrent: 1, // 当前页
        nowData: [],
        columns: [
          {
            title: '操作名称',
            key: 'title'
          },
          {
            title: '请求类型',
            key: 'method'
          },
          {
            title: '请求路径',
            key: 'path'
          },
          {
            title: 'ip地址',
            key: 'ip'
          },
          {
            title: '用户',
            key: 'username'
          },
          {
            title: '时间',
            key: 'created_at'
          }
        ],
        data: [],
        loading: true
      }
    },
    methods: {
      init() {
        getOperationLogs().then(res => {
          this.data = res.result;
          //分页显示所有数据总数
          this.dataCount = this.data.length;
          //循环展示页面刚加载时需要的数据条数
          this.nowData = [];
          for (let i = 0; i < this.pageSize; i++) {
            if (this.data[i]) {
              this.nowData.push(this.data[i]);
            }
          }
          console.log(this.nowData)
          this.loading = false;
        });
      },
      changePage(index) {
        //需要显示开始数据的index,(因为数据是从0开始的，页码是从1开始的，需要-1)
        let _start = (index - 1) * this.pageSize;
        //需要显示结束数据的index
        let _end = index * this.pageSize;
        //截取需要显示的数据
        this.nowData = this.data.slice(_start, _end);
        //储存当前页
        this.pageCurrent = index;
      },
      _nowPageSize(index) {
        //实时获取当前需要显示的条数
        this.pageSize = index;
        this.loadingTable = true;
        this.nowData = [];
        for (let i = 0; i < this.pageSize; i++) {
          if (this.data[i]) {
            this.nowData.push(this.data[i]);
          }
        }
        console.log(this.nowData)
        this.loadingTable = false;
      },
    },
    mounted() {
      this.init();
    }
  }
</script>