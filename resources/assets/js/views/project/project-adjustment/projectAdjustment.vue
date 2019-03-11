<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="projectAdjustment" icon="">发起项目调整</Button>
      <!-- <Button type="error" disabled icon="md-trash">删除</Button> -->
    </p>
  </Card>
</template>
<script>
  import {projectAdjustment} from '../../../api/project';
  import './projectAdjustment.css'

  export default {
    data: function () {
      return {
        isReadOnly: false,
        columns: [],
        data: [],
        tableLoading: true,
        loading: false,
        formId: '',
        form: {},
        index: 1,
        modal: false,
        ruleValidate: {},
      }
    },
    methods: {
      init() {
      },
      projectAdjustment() {
        this.$Modal.confirm({
          title: "确认调整",
          loading: true,
          content: "您确认要发起项目调整?",
          onOk: () => {
            projectAdjustment().then(e => {
              if (e.result) {
                this.$Message.success("调整成功");
              } else {
                this.$Message.error('调整失败!');
              }
              this.$Modal.remove();
              this.loading = false;
            });
          }
        })
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
