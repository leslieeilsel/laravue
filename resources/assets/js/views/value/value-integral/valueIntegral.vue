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
    valueIntegralList
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
            title: '省级',
            key: 'province',
            width: 100,
            // fixed: 'left',
            align: "center"
          },
          {
            title: '本地网',
            key: 'local_wifi',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '三级网格',
            key: 'three_wifi',
            width: 100,
            align: 'left'
          },
          {
            title: 'OBU',
            key: 'obu',
            width: 100,
            align: "center"
          },
          {
            title: '六级网格',
            key: 'six_wifi',
            width: 200,
            align: "left"
          },
          {
            title: '用户发展-单移动',
            key: 'u_single_move',
            width: 100,
            align: "right"
          },
          {
            title: '用户发展-单宽带',
            key: 'u_single_wifi',
            width: 350
          },
          {
            title: '用户发展-融合',
            key: 'u_fuse',
            width: 180,
            align: "right"
          },
          {
            title: '用户发展-政企类产品',
            key: 'u_gover_products',
            width: 350
          },
          {
            title: '存量经营-价值提升',
            key: 's_value',
            width: 180
          },
          {
            title: '存量经营-续费续约',
            key: 's_contract',
            width: 200,
            align: "left"
          },
          {
            title: '存量经营-一般保有',
            key: 's_preservat',
            width: 100,
            align: "right"
          },
          {
            title: '收入贡献-单移动',
            key: 'i_single_move',
            width: 350
          },
          {
            title: '收入贡献-单宽带',
            key: 'i_single_wifi',
            width: 180,
            align: "right"
          },
          {
            title: '收入贡献-融合',
            key: 'i_fuse',
            width: 350
          },
          {
            title: '收入贡献-政企类产品',
            key: 'i_gover',
            width: 350
          },
          {
            title: '服务积分-推广服务',
            key: 'se_exten',
            width: 350
          },
          {
            title: '服务积分-新装服务',
            key: 'se_new',
            width: 350
          },
          {
            title: '服务积分-变更服务',
            key: 'se_change',
            width: 350
          },
          {
            title: '服务积分-畅享迁转',
            key: 'se_migrat',
            width: 350
          },
          {
            title: '服务积分-缴费服务',
            key: 'se_bill',
            width: 350
          }
        ],
        data: [],
        tableLoading: true,
        loading: false,
        searchForm: {
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        }
      }
    },
    methods: {
      init() {
        this.getValueIntegralList();
      },
      getValueIntegralList() {
        this.tableLoading = true;
        valueIntegralList(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          this.searchForm.pageNumber = 1;
          this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getValueIntegralList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getValueIntegralList();
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
