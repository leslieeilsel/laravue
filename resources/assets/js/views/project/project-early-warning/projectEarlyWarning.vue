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
            let button_rbg='success';
            let war_title='已经超额';
            if(params.row.tags==0){
              button_rbg='success';
              war_title='已经超额';
            }else if(params.row.tags==1){
              button_rbg='warning';
              war_title='警告超额';
            }else if(params.row.tags==2){
              button_rbg='error'
              war_title='严重超额';
            }
            return h("div", [
              h(
                "Button",
                {
                  props: {
                    type: button_rbg,
                    size: "small"
                  },
                  style: {
                    marginRight: "5px"
                  },
                },
                war_title
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
                  type: 'primary',
                  size: 'small',
                },
                style: {
                  marginRight: '5px'
                },
                on: {
                  click: () => {
                    this.$router.push({name: 'projects'});
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
