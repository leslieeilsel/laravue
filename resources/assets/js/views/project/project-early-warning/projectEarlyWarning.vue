<template>
  <Card>
    <Table border :columns="columns" :data="data" :loading="loadingTable"></Table>
  </Card>
</template>
<script>
  import {getAllWarning} from '../../../api/project';
  
  export default {
    data() {
      return {
        data: [],
        columns: [{
          title: '项目名称',
          key: 'title',
        }, {
          title: '预警类型',
          key: 'tags',
          render: (h, params) => {
            return h("div", [
              h(
                "Button",
                {
                  props: {
                    type: "primary",
                    size: "small"
                  },
                  style: {
                    marginRight: "5px"
                  },
                },
                params.row.tags
              )
            ]);
          }
        }, {
          title: '操作',
          key: 'action',
          render: (h, params) => {
            return h("div", [
              h('Button', {
                props: {
                  type: 'warning',
                  size: 'small',
                },
                style: {
                  marginRight: '5px'
                },
                on: {
                  click: () => {
                    this.$router.push({name: 'projectInfo'});
                  }
                }
              }, '查看详情')
            ]);
          }
        }],
        loadingTable: true,
      }
    },
    methods: {
      init () {
        getAllWarning().then(res => {
          this.data = res.result;
          this.loadingTable = false;
        });
      },
    },
    mounted() {
      this.init();
    }
  }
</script>
