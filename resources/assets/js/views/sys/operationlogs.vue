<template>
  <Card>
    <Table border stripe :columns="columns" :data="data" :loading="loading"></Table>
  </Card>
</template>
<script>
  import {getOperationLogs} from "../../api/log";

  export default {
    data() {
      return {
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
          this.loading = false;
        });
      },
    },
    mounted() {
      this.init();
    }
  }
</script>