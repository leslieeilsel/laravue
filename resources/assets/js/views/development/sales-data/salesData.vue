<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">销售填报</Button>
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
    <!-- model -->
    <Modal
      :mask-closable="false"
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="销售填报">
      <Form ref="form" :model="form" :label-width="160">
        <Row>
          <Col span="12">
            <FormItem label="用户名" prop="username">
              <Input v-model="form.username" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="用户号码" prop="mobile">
              <Input v-model="form.mobile" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="是否新用户" prop="is_new_user">
              <Select v-model="form.is_new_user" filterable>
                <Option v-for="item in dict.is_new_user" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="产品类型" prop="product_type">
              <Select v-model="form.product_type" filterable>
                <Option v-for="item in dict.product_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="业务类型" prop="business_type">
              <Select v-model="form.business_type" filterable>
                <Option v-for="item in dict.business_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="套餐" prop="meal">
              <Select v-model="form.meal" filterable>
                <Option v-for="item in dict.meal" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="积分" prop="integral">
              <Input type="text" :rows="3" v-model="form.integral" placeholder="请输入..."/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="区域" prop="area">
              <Input type="text" :rows="3" v-model="form.area" placeholder="请输入..."/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="填报人" prop="form_name">
              <Input v-model="form.form_name" type="text" :rows="3" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="日期" prop="date_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.date_time"></DatePicker>
            </FormItem>
          </Col>
        </Row>
      </Form>
      <div slot="footer">
        <Button @click="handleReset('form')" :loading="loading">重置</Button>
        <Button
          @click="submitF('form')"
          :loading="submitLoading"
          type="primary"
          style="margin-left:8px"
        >保存
        </Button>
      </div>
    </Modal>
  </Card>
</template>
<script>
  import {
    salesDataAdd,
    salesDataList,dictData
  } from '../../../api/value';
  import './salesData.css'

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
            title: '用户名',
            key: 'username',
            width: 100,
            // fixed: 'left',
            align: "center"
          },
          {
            title: '用户号码',
            key: 'mobile',
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
            key: 'product_type',
            width: 100,
            align: "center"
          },
          {
            title: '业务类型',
            key: 'business_type',
            width: 200,
            align: "left"
          },
          {
            title: '套餐',
            key: 'meal',
            width: 100,
            align: "right"
          },
          {
            title: '积分',
            key: 'integral',
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
            key: 'form_name',
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
        loading:true,
        modal: false,
        form: {
          username: '',
          mobile: '',
          is_new_user: '',
          business_type: '',
          integral: '',
          area: '',
          form_name: '',
          product_type: '',
          meal: '',
          date_time: '',
        },
        dictName: {
          product_type: '产品类型',
          business_type: '业务类型',
          meal: '套餐',
          is_new_user: '是否新用户'
        },
        dict: {
          product_type: [],
          business_type: [],
          meal: [],
          is_new_user: []
        }
      }
    },
    methods: {
      init() {
        this.getSalesDataList();
        this.getDictData();
      },
      getSalesDataList() {
        this.tableLoading = true;
        salesDataList(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          this.searchForm.pageNumber = 1;
          this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getSalesDataList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getSalesDataList();
      },
      handleReset(name) {
        this.$refs[name].resetFields();
        this.$refs.upload.clearFiles();
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            salesDataAdd(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("销售数据填报成功");
                this.modal = false;
                this.init();
              } else {
                this.$Message.error('销售数据填报失败!');
              }
            });
          }
        })
      },
      cancel() {
        this.$refs.form.resetFields();
        this.handleClearFiles();
      },
      handleClearFiles() {
        this.$refs.upload.clearFiles();
      },
      getDictData() {
        dictData(this.dictName).then(res => {
          console.log(res);
          if (res.result) {
            this.dict = res.result;
          }
        });
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
