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
            title: '用户号码',
            key: 'user_mobile',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '是否新用户',
            key: 'is_new_user',
            width: 100,
            align: 'left'
          },
          {
            title: '产品类型',
            key: 'project_type',
            width: 100,
            align: "center"
          },
          {
            title: '终端类型',
            key: 'terminal_type',
            width: 200,
            align: "left"
          },
          {
            title: '业务类型',
            key: 'business_type',
            width: 200,
            align: "left"
          },
          {
            title: '套餐',
            key: 'set_meal',
            width: 300,
            align: "right"
          },
          {
            title: '积分',
            key: 'int_num',
            width: 350
          },
          {
            title: '区域',
            key: 'area',
            width: 180,
            align: "right"
          },
          {
            title: '填报人',
            key: 'applicant',
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
