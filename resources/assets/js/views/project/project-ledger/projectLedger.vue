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
          <Select v-model="searchForm.project_id"
                  style="width: 200px">
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
      <!-- <Button type="primary" @click="modal = true" icon="md-add">添加台账</Button> -->
      <Button class="exportReport" @click="exportLedger" type="primary" :disabled="btnDisable" icon="md-cloud-upload">导出台账
      </Button>
      <!-- <Button type="error" disabled icon="md-trash">删除</Button> -->
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
          // {
          //   title: '年度',
          //   key: 'year',
          //   width: 100,
          // },
          // {
          //   title: '季度',
          //   key: 'quarter',
          //   width: 100
          // },
          {
            title: '项目名称',
            key: 'project_id',
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
            title: '季度项目进度',
            key: 'quarter_progress',
            width: 180
          },
          {
            title: '存在问题',
            key: 'problem',
            width: 180
          },
          // {
          //     title: '操作',
          //     key: 'action',
          //     width: 150,
          //     fixed: 'right',
          //     align: 'center',
          //     render: (h, params) => {
          //         return h('div', [
          //             h('Button', {
          //                 props: {
          //                     type: 'primary',
          //                     size: 'small'
          //                 },
          //                 style: {
          //                     marginRight: '5px'
          //                 },
          //                 on: {
          //                     click: () => {
          //                         console.log(params)
          //                     }
          //                 }
          //             }, '查看')
          //         ]);
          //     }
          // }
        ],
        data: [],
        tableLoading: true,
        loading: false,
        // form: {
        //   year: '',
        //   quarter: '',
        //   project_id: '',
        //   project_num: '',
        //   nature: '',
        //   subject: '',
        //   total_investors: '',
        //   scale_con: '',
        //   plan_investors: '',
        //   plan_con: '',
        //   quarter_progress: '',
        //   problem: '',
        // },
        index: 1,
        // formValidate: {
        //   year: [
        //     {required: true, type: 'date', message: '年度不能为空', trigger: 'change'}
        //   ],
        //   quarter: [
        //     {required: true, type: 'number', message: '季度不能为空', trigger: 'change'}
        //   ],
        //   project_id: [
        //     {required: true, type: 'number', message: '项目名称不能为空', trigger: 'change'}
        //   ]
        // },
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
          if(res.result.length>0){
            if(search_project_id||start_at||end_at){
              this.btnDisable=false;
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
      // getQuarter() {
      //   getData({title: '季度'}).then(res => {
      //     this.quarter = res.result;
      //   });
      // },
      // getNature(){
      //   getData({title:'建设性质'}).then(res => {
      //     this.nature=res.result;
      //   });
      // },changeYear(e){
      //   this.plan_investors=e+'年计划投资';
      //   this.plan_con=e+'年主要建设内容';
      // },
      // changeProject(e){
      //   this.loading = true;
      //   projectQuarter({year:this.form.year,quarter:this.form.quarter,project_id:e}).then(res => {

      //     let ps=res.result.ProjectSchedules;
      //     let p=res.result.projects;
      //     this.form.project_num=p.num;
      //     this.form.nature=p.build_type;
      //     this.form.subject=p.subject;
      //     this.form.total_investors=p.amount;
      //     this.form.scale_con=p.description;
      //     this.form.plan_investors=ps.plan_investors;
      //     this.form.plan_con=ps.plan_img_progress;
      //     this.form.quarter_progress=ps.start_month_img_progress;
      //     this.form.problem=ps.problem;
      //     this.loading = false;
      //   })
      // },
      // handleSubmit(name) {
      //   console.log(name);
      //     this.$refs[name].validate((valid) => {
      //       if (valid) {
      //         this.submitLoading=true;
      //         projectLedgerAdd(this.form).then(res => {
      //           this.submitLoading=false;
      //           if(res.result){
      //             this.$Message.success("添加成功");
      //             this.modal = false;
      //             this.init();
      //           }else{
      //             this.$Message.error('添加失败!');
      //           }
      //       });
      //     }
      //   })
      // },
      // handleReset (name) {
      //   this.$refs[name].resetFields();
      // },
      cancel() {
        this.$refs.searchForm.resetFields();
      },//导出
      exportLedger(){
        let search_project_id = this.searchForm.project_id;
        let start_at = this.searchForm.start_at;
        let end_at = this.searchForm.end_at;
        let start_time = new Date(start_at);  
        start_time=start_time.getFullYear() + '-' + (start_time.getMonth() + 1) + '-' + start_time.getDate(); 
        let end_time = new Date(end_at);  
        end_time=end_time.getFullYear() + '-' + (end_time.getMonth() + 1) + '-' + end_time.getDate(); 
        console.log(start_at);
        window.location.href="/api/project/exportLedger?search_project_id="+search_project_id+"&start_at="+start_time+"&end_at="+end_time;
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
