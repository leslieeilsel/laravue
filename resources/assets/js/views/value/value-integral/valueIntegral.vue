<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="100" class="search-form">
        <FormItem label="积分日期" prop="date_time">
            <DatePicker type="date" placeholder="积分日期" v-model="searchForm.date_time" style="width: 200px">
            </DatePicker>
        </FormItem>
        <FormItem style="margin-left:-70px;" class="br">
          <Button @click="getValueIntegralList" type="primary" icon="ios-search">检索</Button>
        </FormItem>
      </Form>
    </Row>
    <p class="btnGroup">
      <Upload 
          style="display: inline-block;"
          ref="upload"
          name="import_file"
          :on-success="handleSuccess"
          multiple
          :format="['xls', 'xlsx']"
          :on-format-error="handleFormatError"
          action="/api/value/importValueIntegral">
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
    valueIntegralList,dictData
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
            title: '省',
            key: 'province',
            width: 80,
          },
          {
            title: '本地网',
            key: 'local_wifi',
            width: 100
          },
          {
            title: '三级网格',
            key: 'three_wifi',
            width: 100
          },
          {
            title: 'OBU',
            key: 'obu',
            width: 200
          },
          {
            title: '片区',
            key: 'area',
            width: 240
          },
          {
            title: '六级网格',
            key: 'six_wifi',
            width: 200
          },
          {
            title: '价值提升',
            key: 'stock_v_up',
            width: 100
          },
          {
            title: '续费续约  ',
            key: 'stock_contract',
            width: 100
          },
          {
            title: '一般保有',
            key: 'stock_tenure',
            width: 100
          },
          {
            title: '日期',
            key: 'date_time',
            width: 120
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
        this.getValueIntegralList();
      },
      getValueIntegralList() {
        this.tableLoading = true;
        valueIntegralList(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          // this.searchForm.pageNumber = 1;
          // this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getValueIntegralList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getValueIntegralList();
      },
      handleClearFiles() {
        this.$refs.upload.clearFiles();
      },
      handleSuccess(res, file) {
        this.getValueIntegralList();
        this.$Message.success("导入数据成功");
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
