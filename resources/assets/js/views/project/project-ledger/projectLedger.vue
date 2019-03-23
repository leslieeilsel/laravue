<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="70" class="search-form">
        <Form-item label="台账年份" prop="year_month">
          <Row style="width: 220px">
            <Col span="11">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM" v-model="searchForm.start_at">
              </DatePicker>
            </Col>
            <Col span="2" style="text-align: center">-</Col>
            <Col span="11">
              <DatePicker type="month" placeholder="结束时间" format="yyyy-MM" v-model="searchForm.end_at">
              </DatePicker>
            </Col>
          </Row>
        </Form-item>
        <Form-item label="项目名称" prop="project_id">
          <Select v-model="searchForm.project_id" style="width: 200px">
            <Option value="0" key="0">全部</Option>
            <Option v-for="item in project_id" :value="item.id" :key="item.id">{{ item.title }}</Option>
          </Select>
        </Form-item>
        <Form-item style="margin-left:-35px;" class="br">
          <Button @click="getProjectLedgerList" type="primary" icon="ios-search">搜索</Button>
          <Button @click="cancel">重置</Button>
        </Form-item>
      </Form>
    </Row>
    <p class="btnGroup">
      <Button class="exportReport" @click="exportLedger" type="primary" :disabled="btnDisable" icon="md-cloud-upload">
        导出台账
      </Button>
    </p>
    <Table type="selection" stripe border :columns="columns" :data="nowData" :loading="tableLoading"></Table>
    <Row type="flex" justify="end" class="page">
      <Page :total="dataCount" :page-size="pageSize" @on-change="changePage" @on-page-size-change="_nowPageSize"
            show-total show-sizer :current="pageCurrent"/>
    </Row>
  </Card>
</template>
<script>
  import {initProjectInfo, getData, projectLedgerList, projectQuarter, projectLedgerAdd} from '../../../api/project';
  import './projectLedger.css'

  export default {
    data() {
      return {
        pageSize: 10,   // 每页显示多少条
        dataCount: 0,   // 总条数
        pageCurrent: 1, // 当前页
        nowData: [],
        columns: [
          {
            type: 'index2',
            width: 60,
            align: 'center',
            fixed: 'left',
            render: (h, params) => {
              return h('span', params.index + (this.pageCurrent- 1) * this.pageSize + 1);
            }
          },
          {
            title: '填报时间',
            key: 'month',
            width: 100
          },
          {
            title: '项目名称',
            key: 'title',
            width: 220,
          },
          {
            title: '项目编号',
            key: 'project_num',
            width: 100
          },
          {
            title: '建设性质',
            key: 'nature',
            width: 100
          },
          {
            title: '投资主体',
            key: 'subject',
            width: 100
          },
          {
            title: '总投资',
            key: 'total_investors',
            width: 100
          },
          {
            title: '2019年主要建设规模及主要内容',
            key: 'scale_con',
            width: 210
          },
          {
            title: '2019年计划投资(万元)',
            key: 'plan_investors',
            width: 180
          },
          {
            title: '2019年主要建设内容',
            key: 'plan_con',
            width: 200
          },
          {
            title: '存在问题',
            key: 'problem',
            width: 180
          },
        ],
        data: [],
        tableLoading: true,
        loading: false,
        index: 1,
        btnDisable: true,
        searchForm: {
          project_id: '',
          year: '',
          quarter: ''
        },
        submitLoading: false,
        quarter: [],
        project_id: [],
        modal: false,
      }
    },
    methods: {
      init() {
        this.getProjectLedgerList();
        this.getProjectId();
        // this.getQuarter();
        // this.getNature();
      },
      getProjectLedgerList() {
        let search_project_id = this.searchForm.project_id;
        let start_at = this.searchForm.start_at;
        let end_at = this.searchForm.end_at;

        this.tableLoading = true;
        projectLedgerList({
          search_project_id: search_project_id,
          start_at: start_at,
          end_at: end_at
        }).then(res => {
          this.data = res.result;
          //分页显示所有数据总数
          this.dataCount = this.data.length;
          //循环展示页面刚加载时需要的数据条数
          this.nowData = [];
          for (let i = 0; i < this.pageSize; i++) {
            if (this.data[i]) {
              this.nowData.push(this.data[i]);
            }
          }
          this.pageCurrent = 1;
          if (res.result) {
            if (search_project_id || start_at || end_at) {
              this.btnDisable = false;
            }
          }
          this.tableLoading = false;
        })
      },
      getProjectId() {
        initProjectInfo().then(res => {
          this.project_id = res.result;
        });
      },
      cancel() {
        this.$refs.searchForm.resetFields();
      },//导出
      exportLedger() {
        let search_project_id = this.searchForm.project_id;
        let start_at = this.searchForm.start_at;
        let end_at = this.searchForm.end_at;
        let start_time = '';
        if (start_at) {
          let start_time_0 = new Date(start_at);
          let month_start_time_0 = (start_time_0.getMonth() + 1) > 9 ? (start_time_0.getMonth() + 1) : '0' + (start_time_0.getMonth() + 1);
          start_time = start_time_0.getFullYear() + '-' + month_start_time_0;
        }
        let end_time = '';
        if (end_at) {
          let end_time_0 = new Date(end_at);
          let month_end_time_0 = (end_time_0.getMonth() + 1) > 9 ? (end_time_0.getMonth() + 1) : '0' + (end_time_0.getMonth() + 1);
          end_time = end_time_0.getFullYear() + '-' + month_end_time_0;
        }
        window.location.href = "/api/project/exportLedger?search_project_id=" + search_project_id + "&start_at=" + start_time + "&end_at=" + end_time;
      },
      changePage(index) {
        //需要显示开始数据的index,(因为数据是从0开始的，页码是从1开始的，需要-1)
        let _start = (index - 1) * this.pageSize;
        //需要显示结束数据的index
        let _end = index * this.pageSize;
        //截取需要显示的数据
        this.nowData = this.data.slice(_start, _end);
        //储存当前页
        this.pageCurrent = index;
      },
      _nowPageSize(index) {
        //实时获取当前需要显示的条数
        this.pageSize = index;
        this.loadingTable = true;
        this.nowData = [];
        for (let i = 0; i < this.pageSize; i++) {
          if (this.data[i]) {
            this.nowData.push(this.data[i]);
          }
        }
        this.pageCurrent = 1;
        this.loadingTable = false;
      },
    },
    mounted() {
      this.init();
    }
  }
</script>
