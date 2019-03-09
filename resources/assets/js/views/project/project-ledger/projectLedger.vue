<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">添加台账</Button>
      <!-- <Button type="error" disabled icon="md-trash">删除</Button> -->
    </p>
    <Table type="selection" stripe border :columns="columns" :data="data" :loading="tableLoading"></Table>
    <Modal
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="创建项目">
      <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="110">
        <Row>
          <Col span="11">
                <FormItem label="台账年份" prop="month">
                  <DatePicker type="year" placeholder="台账年份" format="yyyy"
                              v-model="form.month"></DatePicker>
                </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="季度" prop="quarter">
              <Select v-model="form.quarter">
                <Option v-for="item in quarter" :value="item.id" :key="item.id" disabled>{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
              <FormItem label="项目名称" prop="project_id">
                    <Select v-model="form.project_id"  @on-change="changeProject" >
                      <Option v-for="item in project_id" :value="item.id" :key="item.id">{{ item.title }}</Option>
                    </Select>
              </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
              <FormItem label="项目编号" prop="project_num">
                  <Input v-model="form.project_num" placeholder="必填项" readonly></Input>
              </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="建设性质" prop="nature">
              <Select v-model="form.nature">
                    <Option v-for="item in nature" :value="item.id" :key="item.id">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="2"></Col>
            <Col span="11">
                <FormItem label="投资主体" prop="subject">
                    <Input v-model="form.subject" placeholder="必填项" readonly></Input>
                </FormItem>
            </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="总投资" prop="total_investors">
              <Input v-model="form.total_investors" placeholder="支持小数点后两位"/>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="scale_con" prop="scale_con">
              <Input v-model="form.scale_con"  type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="必填项"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
              <FormItem :label="plan_investors" prop="plan_investors">
                  <Input v-model="form.plan_investors" placeholder="必填项"/>
              </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="plan_con" prop="plan_con">
              <Input v-model="form.plan_con"  type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="必填项"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem :label="quarter_progress" prop="quarter_progress">
              <Input v-model="form.quarter_progress" placeholder="必填项"></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="form.problem"  type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="必填项"></Input>
            </FormItem>
          </Col>
        </Row>
      </Form>
      <div slot="footer">
        <Button @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
        <Button type="primary" @click="handleSubmit('formValidate')" :loading="loading">提交</Button>
      </div>
    </Modal>
  </Card>
</template>
<script>
  import {initProjectInfo,getData} from '../../../api/project';
  import './projectLedger.css'
  
  export default {
    data () {
      return {
        columns: [
          {
            type: 'selection',
            width: 60,
            align: 'center',
            fixed: 'left'
          },
          {
            title: '年度',
            key: 'year',
            width: 220,
            fixed: 'left'
          },
          {
            title: '季度',
            key: 'quarter',
            width: 100
          },
          {
            title: '项目名称',
            key: 'title',
            width: 220,
            fixed: 'left'
          },
          {
            title: '项目编号',
            key: 'num',
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
            width: 210
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
            title: '2019年计划投资',
            key: 'plan_investors',
            width: 100
          },
          {
            title: '2019年主要建设内容',
            key: 'plan_con',
            width: 100
          },
          {
            title: '季度项目进度',
            key: 'quarter_progress',
            width: 100
          },
          {
            title: '存在问题',
            key: 'problem',
            width: 140
          },
          {
              title: '操作',
              key: 'action',
              width: 150,
              fixed: 'right',
              align: 'center',
              render: (h, params) => {
                  return h('div', [
                      h('Button', {
                          props: {
                              type: 'primary',
                              size: 'small'
                          },
                          style: {
                              marginRight: '5px'
                          },
                          on: {
                              click: () => {
                                  console.log(params)
                              }
                          }
                      }, '查看'),
                      h('Button', {
                          props: {
                              type: 'error',
                              size: 'small'
                          },
                          on: {
                              click: () => {
                                console.log(params)
                              }
                          }
                      }, '审核')
                  ]);
              }
          }
        ],
        data: [],
        tableLoading: true,
        loading: false,
        form: {
          year: '',
          quarter: '',
          project_id: '',
          project_num: '',
          nature: '',
          subject: '',
          total_investors: '',
          scale_con: '',
          plan_investors: '',
          plan_con: '',
          quarter_progress: '',
          problem: '',
        },
        index: 1,
        modal: false,
        ruleValidate: {
          year: [
            {required: true, message: '年度不能为空', trigger: 'blur'}
          ],
          quarter: [
            {required: true, message: '季度不能为空', trigger: 'blur'}
          ],
          project_id: [
            {required: true, message: '项目名称不能为空', trigger: 'blur'}
          ]
        },
        quarter:[],
        project_id:[],
        nature:[],
        scale_con: '2019年主要建设规模及主要内容',
        plan_investors: '2019年项目计划投资',
        plan_con: '2019年主要建设内容',
        quarter_progress: '2019年主要建设内容',
      }
    },
    methods: {
      init() {
        this.$refs.formValidate.resetFields();
        this.getProjectId();
        this.getQuarter();
        this.getNature();
      },
      getProjectId(){
        initProjectInfo().then(res => {
          this.project_id=res.result;
        });
      },
      getQuarter(){
        getData({title:'季度'}).then(res => {
          this.quarter=res.result;
        });
      },
      getNature(){
        getData({title:'建设性质'}).then(res => {
          console.log(res);
          
          this.nature=res.nature;
        });
      },
      changeProject(e){
        let _this = this;
        this.project_id.forEach(function (em) {
          if (em.id===e) {
            // _this.form.subject=em.subject;
            // _this.form.project_num=em.num;
            // _this.form.build_start_at=em.plan_start_at;
            // _this.form.build_end_at=em.plan_end_at;
            // _this.form.total_investors=em.amount;
            // _this.form.plan_start_at=em.plan_start_at;
            // if(_this.form.month){
            //   projectPlanInfo({month:_this.form.month}).then(res => {
            //       _this.form.plan_investors=res.result.amount;
            //       _this.form.plan_img_progress=res.result.image_progress;
            //   });
            // }
          }
        })
      },
      handleReset (name) {
        this.$refs[name].resetFields();
      },
      cancel() {
        this.$refs.formValidate.resetFields();
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
