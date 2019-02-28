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
      width="540"
      title="创建项目">
      <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="110">
        <FormItem label="项目名称" prop="title">
          <Input v-model="form.title" placeholder="必填项"/>
        </FormItem>
        <FormItem label="项目编号" prop="num">
          <Input v-model="form.num" placeholder="必填项"></Input>
        </FormItem>
        <FormItem label="建设状态" prop="status">
          <Select v-model="form.status">
            <Option value="计划">计划</Option>
            <Option value="在建">在建</Option>
            <Option value="建成">建成</Option>
          </Select>
        </FormItem>
        <FormItem label="业主" prop="owner">
          <Input v-model="form.owner" placeholder="必填项"/>
        </FormItem>
        <FormItem label="项目类型" prop="type">
          <Select v-model="form.type">
            <Option value="房建">房建</Option>
            <Option value="市政">市政</Option>
            <Option value="绿化">绿化</Option>
            <Option value="水利">水利</Option>
          </Select>
        </FormItem>
        <FormItem label="建设单位" prop="unit">
          <Input v-model="form.unit" placeholder="必填项"></Input>
        </FormItem>
        <FormItem label="项目金额" prop="amount">
          <Input v-model="form.amount" placeholder="支持小数点后两位"/>
        </FormItem>
        <FormItem label="项目标识" prop="is_gc">
          <Select v-model="form.is_gc">
            <Option value="是改创项目">是改创项目</Option>
            <Option value="不是改创项目">不是改创项目</Option>
          </Select>
        </FormItem>
        <FormItem label="项目概况" prop="description">
            <Input v-model="form.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="请输入..."></Input>
        </FormItem>
        <FormItem label="计划时间">
          <Row>
            <Col span="11">
              <DatePicker type="date" placeholder="开始时间" v-model="form.plan_start_at"></DatePicker>
            </Col>
            <Col span="2" style="text-align: center">-</Col>
            <Col span="11">
              <DatePicker type="date" placeholder="结束时间" v-model="form.plan_end_at"></DatePicker>
            </Col>
          </Row>
        </FormItem>
        <FormItem label="实际时间">
          <Row>
            <Col span="11">
              <DatePicker type="date" placeholder="开始时间" v-model="form.actual_start_at"></DatePicker>
            </Col>
            <Col span="2" style="text-align: center">-</Col>
            <Col span="11">
              <DatePicker type="date" placeholder="结束时间" v-model="form.actual_end_at"></DatePicker>
            </Col>
          </Row>
        </FormItem>
        <FormItem label="项目中心点坐标" prop="center_point">
          <Input v-model="form.center_point" placeholder="必填项"/>
        </FormItem>
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
            title: '项目标识',
            key: 'is_gc',
            width: 120
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
            title: '实际开始时间',
            key: 'actual_start_at',
            width: 120
          },
          {
            title: '实际结束时间',
            key: 'actual_end_at',
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
                                  this.show(params.index)
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
                                  this.remove(params.index)
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
          type: '',
          status: '',
          unit: '',
          amount: '',
          is_gc: '',
          plan_start_at: '',
          plan_end_at: '',
          actual_start_at: '',
          actual_end_at: '',
          center_point: '',
          description: '',
          positions: [
            {
              value: '',
              index: 1,
              status: 1
            }
          ]
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
          type: [
            {required: true, message: '项目类型不能为空', trigger: 'blur'}
          ],
          amount: [
            {required: true, message: '项目金额不能为空', trigger: 'blur'}
          ],
          is_gc: [
            {required: true, message: '项目标识不能为空', trigger: 'blur'}
          ],
          unit: [
            {required: true, message: '建设单位不能为空', trigger: 'blur'}
          ],
          center_point: [
            {required: true, message: '项目中心点坐标不能为空', trigger: 'blur'}
          ]
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
            this.$Message.error('发生错误!');
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
    },
    mounted() {
      this.init();
    }
  }
</script>
