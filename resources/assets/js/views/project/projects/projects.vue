<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">添加项目</Button>
      <Button type="error" disabled icon="md-trash">删除</Button>
    </p>
    <Table type="selection" stripe border :columns="columns" :data="data" :loading="tableLoading"></Table>
    <Modal
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="创建项目">
      <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="110">
        <Divider><h4>基本信息</h4></Divider>
        <Row>
          <Col span="11">
            <FormItem label="项目名称" prop="title">
              <Input v-model="form.title" placeholder="必填项"/>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="项目编号" prop="num">
              <Input v-model="form.num" placeholder="必填项"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="业主" prop="owner">
              <Input v-model="form.owner" placeholder="必填项"/>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="form.subject" placeholder="必填项"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="项目类型" prop="type">
              <Select v-model="form.type">
                <Option value="房建">房建</Option>
                <Option value="市政">市政</Option>
                <Option value="绿化">绿化</Option>
                <Option value="水利">水利</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="建设单位" prop="unit">
              <Input v-model="form.unit" placeholder="必填项"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="项目金额" prop="amount">
              <Input v-model="form.amount" placeholder="支持小数点后两位"/>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="建安投资" prop="safe_amount">
              <Input v-model="form.safe_amount" placeholder="支持小数点后两位"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="土地费用" prop="land_amount">
              <Input v-model="form.land_amount" placeholder="支持小数点后两位"/>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="项目标识" prop="is_gc">
              <Select v-model="form.is_gc">
                <Option value="是国民经济计划">是国民经济计划</Option>
                <Option value="不是国民经济计划">不是国民经济计划</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="计划开始时间" prop="plan_start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy年MM月" v-model="form.plan_start_at"></DatePicker>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="计划结束时间" prop="plan_end_at">
              <DatePicker type="month" @on-change="buildYearPlan" placeholder="结束时间" format="yyyy年MM月" v-model="form.plan_end_at"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="项目中心点坐标" prop="center_point">
              <Input v-model="form.center_point" placeholder="必填项"/>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="项目状态" prop="status">
              <Select v-model="form.status">
                <Option value="在建">在建</Option>
                <Option value="已建">已建</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem
              v-for="(item, index) in form.positions"
              v-if="item.status"
              :key="index"
              :label="'项目轮廓坐标 ' + item.index"
              :prop="'positions.' + index + '.value'"
              :rules="{required: true, message: '坐标点 ' + item.index +' 不能为空', trigger: 'blur'}">
              <Row>
                <Col span="18">
                  <Input type="text" v-model="item.value" placeholder="请输入坐标"></Input>
                </Col>
                <Col span="4" offset="1">
                  <Button @click="handleRemove(index)">移除</Button>
                </Col>
              </Row>
            </FormItem>
            <FormItem>
              <Row>
                <Col span="12">
                  <Button type="dashed" long @click="handleAdd" icon="md-add">添加坐标点</Button>
                </Col>
              </Row>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="项目概况" prop="description">
              <Input v-model="form.description" type="textarea" :rows="4" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Divider><h4>投资计划</h4></Divider>
        <div v-for="(item, index) in form.projectPlan">
          <Divider orientation="left"><h5>{{item.date}}年项目计划</h5></Divider>
          <Row>
            <Col span="11">
              <FormItem
                label="计划投资金额"
                :prop="'projectPlan.' + index + '.amount'"
                :rules="{required: true, message: '计划投资金额不能为空', trigger: 'blur'}">
                <Input v-model="item.amount" placeholder="支持小数点后两位"/>
              </FormItem>
            </Col>
            <Col span="2"></Col>
            <Col span="11">
              <FormItem
                label="计划形象进度"
                :rules="{required: true, message: '计划形象进度不能为空', trigger: 'blur'}"
                :prop="'projectPlan.' + index + '.image_progress'">
                <Input v-model="item.image_progress" type="textarea" :rows="1" placeholder="请输入..."></Input>
              </FormItem>
            </Col>
          </Row>
        </div>
      </Form>
      <div slot="footer">
        <Button @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
        <Button type="primary" @click="handleSubmit('formValidate')" :loading="loading">提交</Button>
      </div>
    </Modal>
  </Card>
</template>
<script>
  import {getAllProjects, addProject} from '../../../api/project';
  import './projects.css'
  
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
            title: '建设状态',
            key: 'status',
            width: 100
          },
          {
            title: '业主',
            key: 'owner',
            width: 100
          },
          {
            title: '投资主体',
            key: 'subject',
            width: 210
          },
          {
            title: '项目类型',
            key: 'type',
            width: 100
          },
          {
            title: '建设单位',
            key: 'unit',
            width: 210
          },
          {
            title: '项目金额',
            key: 'amount',
            width: 100
          },
          {
            title: '建安投资',
            key: 'safe_amount',
            width: 100
          },
          {
            title: '土地费用',
            key: 'land_amount',
            width: 100
          },
          {
            title: '项目标识',
            key: 'is_gc',
            width: 140
          },
          {
            title: '计划开始时间',
            key: 'plan_start_at',
            width: 120
          },
          {
            title: '计划结束时间',
            key: 'plan_end_at',
            width: 120
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
          title: '',
          num: '',
          owner: '',
          subject: '',
          type: '',
          status: '',
          unit: '',
          amount: '',
          safe_amount: '',
          land_amount: '',
          is_gc: '',
          plan_start_at: '',
          plan_end_at: '',
          center_point: '',
          description: '',
          positions: [
            {
              value: '',
              index: 1,
              status: 1
            }
          ],
          projectPlan: [
            {
              date: '2019',
              amount: '',
              image_progress: ''
            },
          ],
        },
        index: 1,
        modal: false,
        ruleValidate: {
          title: [
            {required: true, message: '项目名称不能为空', trigger: 'blur'}
          ],
          num: [
            {required: true, message: '项目编号不能为空', trigger: 'blur'}
          ],
          status: [
            {required: true, message: '建设状态不能为空', trigger: 'blur'}
          ],
          owner: [
            {required: true, message: '业主不能为空', trigger: 'blur'}
          ],
          subject: [
            {required: true, message: '投资主体不能为空', trigger: 'blur'}
          ],
          type: [
            {required: true, message: '项目类型不能为空', trigger: 'blur'}
          ],
          amount: [
            {required: true, message: '项目金额不能为空', trigger: 'blur'}
          ],
          safe_amount: [
            {required: true, message: '建安投资不能为空', trigger: 'blur'}
          ],
          land_amount: [
            {required: true, message: '土地费用不能为空', trigger: 'blur'}
          ],
          is_gc: [
            {required: true, message: '项目标识不能为空', trigger: 'blur'}
          ],
          unit: [
            {required: true, message: '建设单位不能为空', trigger: 'blur'}
          ],
          center_point: [
            {required: true, message: '项目中心点坐标不能为空', trigger: 'blur'}
          ],
          plan_start_at:[
           { required: true, message: '预送达时间不能为空', trigger: 'change' ,type: 'date'},
         ],
          plan_end_at:[
            { required: true, message: '预送达时间不能为空', trigger: 'change' ,type: 'date'},
          ],
        },
      }
    },
    methods: {
      init() {
        this.$refs.formValidate.resetFields();
        this.getProject();
      },
      getProject() {
        this.tableLoading = true;
        getAllProjects().then(e => {
          this.data = e.result;
          this.tableLoading = false;
        });
      },
      handleSubmit (name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            addProject(this.form).then(e => {
              if (e.result) {
                this.$Message.success('添加成功!');
                this.loading = false;
                this.modal = false;
                this.init();
              } else {
                this.$Message.error('添加失败!');
                this.loading = false;
              }
            });
          } else {
            this.$Message.error('请填写必填字段!');
          }
        })
      },
      handleReset (name) {
        this.$refs[name].resetFields();
      },
      handleAdd () {
        this.index++;
        this.form.positions.push({
          value: '',
          index: this.index,
          status: 1
        });
      },
      handleRemove (index) {
        this.form.positions[index].status = 0;
      },
      cancel() {
        this.$refs.formValidate.resetFields();
        this.form.group_id = this.checkedDefaultRole;
      },
      buildYearPlan: function (date) {
        let startDate = this.form.plan_start_at;
        let startYear = startDate.toString().slice(11, 15);
        let endDate = this.form.plan_end_at;
        let endYear = endDate.toString().slice(11, 15);
        let yearPlanArr = [];
        for (let i = startYear; i <= endYear; i++) {
          yearPlanArr.push({
            date: i,
            amount: '',
            image_progress: ''
          });
        }
        this.form.projectPlan = yearPlanArr;
      },
    },
    mounted() {
      this.init();
    }
  }
</script>
