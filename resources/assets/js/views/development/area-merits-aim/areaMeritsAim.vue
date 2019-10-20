<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">片区目标填报</Button>
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
            <FormItem label="目标责任部门" prop="duty_department">
              <Input v-model="form.duty_department" placeholder="" ></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="目标责任人" prop="duty_user">
              <Input v-model="form.duty_user" placeholder="" ></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="目标时间" prop="target_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.target_time"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="业务类型" prop="business_type">
              <Select v-model="form.business_type" filterable>
                <Option v-for="item in dict.business_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="产品类型" prop="product_type">
              <Select v-model="form.product_type" filterable>
                <Option v-for="item in dict.product_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="销售目标" prop="target">
              <Input v-model="form.target" type="textarea" :rows="3" placeholder="请输入..."></Input>
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
    areaMeritsAimAdd,
    areaMeritsAimList,
    dictData
  } from '../../../api/value';
  import './areaMeritsAim.css'

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
            title: '目标责任部门',
            key: 'duty_department',
            width: 200,
            // fixed: 'left',
            align: "center"
          },
          {
            title: '目标责任人',
            key: 'duty_user',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '目标时间',
            key: 'target_time',
            width: 240,
            align: 'left'
          },
          {
            title: '业务类型',
            key: 'business_type',
            width: 100,
            align: "center"
          },
          {
            title: '产品类型',
            key: 'product_type',
            width: 200,
            align: "left"
          },
          {
            title: '销售目标',
            key: 'target',
            width: 100,
            align: "right"
          }
        ],
        data: [],
        tableLoading: true,
        loading: false,
        modal: false,
        searchForm: {
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        submitLoading: false,
        loading:true,
        form: {
          duty_department: '',
          duty_user: '',
          target_time: '',
          product_type: '',
          target: '',
          business_type: '',
        },
        dictName: {
          product_type: '产品类型',
          business_type: '业务类型',
        },
        dict: {
          product_type: [],
          business_type: []
        }
      }
    },
    methods: {
      init() {
        this.getAreaMeritsAimList();
        this.getDictData();
      },
      getAreaMeritsAimList() {
        this.tableLoading = true;
        areaMeritsAimList(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          this.searchForm.pageNumber = 1;
          this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getAreaMeritsAimList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getAreaMeritsAimList();
      },
      handleReset(name) {
        this.$refs[name].resetFields();
        this.$refs.upload.clearFiles();
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            areaMeritsAimAdd(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("片区目标填报成功");
                this.modal = false;
                this.init();
              } else {
                this.$Message.error('片区目标填报失败!');
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
