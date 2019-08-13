<template>
  <Card>
    <Table border stripe :columns="columns" :data="data" :loading="loading"></Table>
    <Row type="flex" justify="end" class="page">
      <Page
        :total="dataCount"
        :page-size="searchForm.pageSize"
        @on-change="changePage"
        @on-page-size-change="changePageSize"
        show-total
        :current="searchForm.pageNumber"/>
    </Row>
  </Card>
</template>
<script>
  import {getOperationLogs} from "../../api/log";

  export default {
    data() {
      return {
        dataCount: 0,   // 总条数
        searchForm: {
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
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
        this.getLogs();
      },
      getLogs() {
        this.loading = true;
        getOperationLogs(this.searchForm).then(res => {
          this.data = res.result;
          this.dataCount = res.total;
          this.loading = false;
        });
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getLogs();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getLogs();
      },
    },
    mounted() {
      this.init();
    }
  }
</script>