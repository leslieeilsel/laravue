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
            <Option value="-1" key="-1">全部</Option>
            <Option v-for="item in project_id" :value="item.id" :key="item.id">{{ item.title }}</Option>
          </Select>
        </Form-item>
        <span v-if="drop">
          <FormItem label="资金来源">
            <Select v-model="searchForm.money_from" prop="money_from" style="width: 200px">
              <Option value="-1" key="-1">全部</Option>
              <Option v-for="item in dict.money_from" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="项目标识" prop="is_gc">
            <Select @on-change="onSearchIsGcChange" v-model="searchForm.is_gc" style="width: 200px"
                    placeholder="是否为国民经济计划">
              <Option value="-1" key="-1">全部</Option>
              <Option v-for="item in dict.is_gc" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
          <FormItem label="国民经济计划分类" prop="nep_type">
            <Select v-model="searchForm.nep_type" style="width: 200px" :disabled="searchNepDisabled">
              <Option value="-1" key="-1">全部</Option>
              <Option v-for="item in dict.nep_type" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
        </span>
        <Form-item style="margin-left:-35px;" class="br">
          <Button @click="searchProjectLedgerList" type="primary" icon="ios-search">搜索</Button>
          <Button @click="cancel">重置</Button>
          <a class="drop-down" @click="dropDown">
            {{dropDownContent}}
            <Icon :type="dropDownIcon"></Icon>
          </a>
        </Form-item>
      </Form>
    </Row>
    <p class="btnGroup">
      <Button class="exportReport" @click="exportLedger" type="primary" :disabled="btnDisable" icon="md-cloud-upload">
        导出台账
      </Button>
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
    initProjectInfo, getData, projectLedgerList, projectQuarter, projectLedgerAdd,
    getProjectDictData
  } from '../../../api/project';
  import './projectLedger.css'

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
            title: '填报时间',
            key: 'month',
            width: 100,
            fixed: 'left',
            align: "center"
          },
          {
            title: '项目名称',
            key: 'title',
            fixed: 'left',
            width: 220,
          },
          {
            title: '项目编号',
            key: 'project_num',
            width: 100,
            align: 'left'
          },
          {
            title: '建设性质',
            key: 'nature',
            width: 100,
            align: "center"
          },
          {
            title: '投资主体',
            key: 'subject',
            width: 200,
            align: "left"
          },
          {
            title: '总投资',
            key: 'total_investors',
            width: 100,
            align: "right"
          },
          {
            title: '项目建设规模及主要内容',
            key: 'description',
            width: 350
          },
          {
            title: '2019年计划投资(万元)',
            key: 'plan_investors',
            width: 180,
            align: "right"
          },
          {
            title: '年度项目主要建设内容',
            key: 'plan_img_progress',
            width: 350
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
          start_at: '',
          end_at: '',
          money_from: '',
          is_gc: '',
          nep_type: '',
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        submitLoading: false,
        quarter: [],
        project_id: [],
        modal: false,
        dictName: {
          is_gc: '是否为国民经济计划',
          nep_type: '国民经济计划分类',
          money_from: '资金来源',
        },
        dict: {
          is_gc: [],
          nep_type: [],
          money_from: []
        },
        searchNepDisabled: true,
        dropDownIcon: "ios-arrow-down",
        dropDownContent: '展开',
        drop: false,
        clickSearch: false,
      }
    },
    methods: {
      init() {
        this.getProjectLedgerList();
        this.getProjectId();
        // this.getQuarter();
        // this.getNature();
        this.getDictData();
      },
      searchProjectLedgerList() {
        this.clickSearch = true;
        this.getProjectLedgerList();
      },
      getProjectLedgerList() {
        this.tableLoading = true;
        projectLedgerList(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          this.btnDisable = !this.clickSearch;
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
        let money_from = this.searchForm.money_from;
        let is_gc = this.searchForm.is_gc;
        let nep_type = this.searchForm.nep_type;
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
        window.location.href = "/api/project/exportLedger?project_id=" + search_project_id + "&start_at=" + start_time + "&end_at=" + end_time + "&money_from=" + money_from + "&is_gc=" + is_gc + "&nep_type=" + nep_type;
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getProjectLedgerList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getProjectLedgerList();
      },
      getDictData() {
        getProjectDictData(this.dictName).then(res => {
          if (res.result) {
            this.dict = res.result;
          }
        });
      },
      onSearchIsGcChange(value) {
        this.searchNepDisabled = value !== 1;
        if (this.searchNepDisabled) {
          this.searchForm.nep_type = '';
        }
      },
      dropDown() {
        if (this.drop) {
          this.dropDownContent = "展开";
          this.dropDownIcon = "ios-arrow-down";
        } else {
          this.dropDownContent = "收起";
          this.dropDownIcon = "ios-arrow-up";
        }
        this.drop = !this.drop;
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
