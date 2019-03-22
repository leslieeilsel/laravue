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
    <Table type="selection" stripe border :columns="columns" :data="data" :loading="tableLoading"></Table>
  
  </Card>
</template>
<script>
  import {initProjectInfo, getData, projectLedgerList, projectQuarter, projectLedgerAdd} from '../../../api/project';
  import './projectLedger.css'

  export default {
    data() {
      return {
        columns: [
          {
            type: 'selection',
            width: 60,
            align: 'center',
            fixed: 'left'
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
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
