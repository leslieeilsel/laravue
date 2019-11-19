<template>
  <Card>
    <p class="btnGroup">
      <Upload 
          style="display: inline-block;"
          ref="upload"
          name="import_file"
          :on-success="handleSuccess"
          multiple
          :format="['xls', 'xlsx']"
          :on-format-error="handleFormatError"
          action="/api/value/importIntegral">
          <Button type="primary" icon="ios-cloud-upload-outline">导入积分</Button>
      </Upload>
      <!-- <Button type="primary" @click="importIntegral" icon="md-add">导入积分</Button> -->
    </p>
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
    devIntegralList,dictData
  } from '../../../api/value';
  import './devIntegral.css'

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
            title: '省',
            key: 'province',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '本地网',
            key: 'local_wifi',
            width: 100,
            align: 'left'
          },
          {
            title: '三级网络',
            key: 'three_wifi',
            width: 100,
            align: "center"
          },
          {
            title: 'OBU',
            key: 'obu',
            width: 200,
            align: "left"
          },
          {
            title: '片区',
            key: 'area',
            width: 200,
            align: "left"
          },
          {
            title: '六级网络',
            key: 'six_wifi',
            width: 300,
            align: "right"
          },
          {
            title: '单移动',
            key: 'u_single_move',
            width: 350
          },
          {
            title: '单宽带',
            key: 'u_single_wifi',
            width: 350
          },
          {
            title: '融合  ',
            key: 'u_fuse',
            width: 180,
            align: "right"
          },
          {
            title: '政企类产品',
            key: 'u_gover_products',
            width: 350
          },
          {
            title: '日期',
            key: 'date_time',
            width: 180
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
        this.getDevIntegralList();
      },
      getDevIntegralList() {
        this.tableLoading = true;
        devIntegralList(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          this.searchForm.pageNumber = 1;
          this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getDevIntegralList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getDevIntegralList();
      },
      handleClearFiles() {
        this.$refs.upload.clearFiles();
      },
      handleSuccess(res, file) {
        this.$Message.success("导入数据成功");
        console.log(res);
        
      },
      handleFormatError(file) {
        this.$Notice.warning({
          title: '文件格式不正确',
          desc: '文件格式不正确，请选择xls或xlsx'
        });
      },
      importIntegral(){

      }
    },
    mounted() {
      this.init();
    }
  }
</script>
