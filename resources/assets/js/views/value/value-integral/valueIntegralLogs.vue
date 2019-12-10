<template>
  <Card>
    <Table type="selection" stripe border :columns="columns" :data="data" :loading="tableLoading"></Table>
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
  import {
    valueIntegralLogs,valueIntegralLogsDel,dictData
  } from '../../../api/value';
  import './valueIntegral.css'

  export default {
    data() {
      return {
        dataCount: 0,   // 总条数
        columns: [
          {
            type: 'index2',
            width: 50,
            align: 'center',
            fixed: 'left',
            render: (h, params) => {
              return h('span', params.index + (this.searchForm.pageNumber - 1) * this.searchForm.pageSize + 1);
            }
          },
          {
            title: '名称',
            key: 'title',
            width: 280,
          },
          {
            title: '表名',
            key: 'table_name',
            width: 200
          },
          {
            title: '详情',
            key: 'desc',
            width: 200
          },
          {
            title: '日期',
            key: 'created_at',
            width: 220
          },
          {
            title: '操作',
            key: 'action',
            width: 180,
            fixed: 'right',
            align: 'center',
            render: (h, params) => {
              let delButton;
              return h('div', [
                h('Button', {
                  props: {
                    type: 'error',
                    size: 'small',
                    disabled: delButton,
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      this.$Modal.confirm({
                        title: "确认删除",
                        loading: true,
                        content: "您确认要删除导入【"+params.row.desc+"】数据？",
                        onOk: () => {
                          valueIntegralLogsDel({id: params.row.id}).then(res => {
                            if (res.result === true) {
                              this.$Message.success("删除成功");
                              this.init();
                            } else {
                              this.$Message.error("删除失败");
                            }
                            this.$Modal.remove();
                          });
                        }
                      });
                    }
                  }
                }, '删除')
              ])
            }
          }
        ],
        data: [],
        tableLoading: true,
        loading: false,
        searchForm: {
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        submitLoading: false,
        modal: false
      }
    },
    methods: {
      init() {
        this.getValueIntegralLogs();
      },
      getValueIntegralLogs() {
        this.tableLoading = true;
        valueIntegralLogs(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          // this.searchForm.pageNumber = 1;
          // this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getValueIntegralLogs();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getValueIntegralLogs();
      },
      handleClearFiles() {
        this.$refs.upload.clearFiles();
      },
    },
    mounted() {
      this.init();
    }
  }
</script>
