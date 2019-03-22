<template>
  <Card>
    <Alert type="warning" class="description">
      <Row>
        <Button type="success" size="small">已经超额</Button>
        <span>：相比预期延缓<10%</span>
      </Row>
      <Row>
        <Button type="warning" size="small">警告超额</Button>
        <span>：相比预期延缓10% ~ 20%</span>
      </Row>
      <Row>
        <Button type="error" size="small">严重超额</Button>
        <span>：相比预期延缓>20%</span>
      </Row>
    </Alert>
    <Table border :columns="columns" :data="data" :loading="loadingTable"></Table>
    <Modal
      v-model="previewModal"
      :styles="{top: '20px'}"
      width="850"
      title="查看项目">
      <Form ref="previewFormValidate" :model="previewForm" :label-width="110">
        <Divider><h4>基础信息</h4></Divider>
        <Row>
          <Col span="12">
            <FormItem label="项目名称" prop="title">
              <Input v-model="previewForm.title" placeholder="" v-bind:readonly="isReadOnly"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目编号" prop="num">
              <Input v-model="previewForm.num" placeholder="" v-bind:readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目类型" prop="type">
              <Input v-model="previewForm.type" placeholder="" v-bind:readonly="isReadOnly"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="previewForm.subject" placeholder="" v-bind:readonly="isReadOnly"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="建设性质" prop="build_type">
              <Input v-model="previewForm.build_type" placeholder="" v-bind:readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="承建单位" prop="unit">
              <Input v-model="previewForm.unit" placeholder="" v-bind:readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="资金来源" prop="money_from">
              <Input v-model="previewForm.money_from" placeholder="" v-bind:readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目金额(万元)" prop="amount">
              <Input v-model="previewForm.amount" placeholder="" v-bind:readonly="isReadOnly"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="土地费用(万元)" prop="land_amount">
              <Input v-model="previewForm.land_amount" placeholder="" v-bind:readonly="isReadOnly"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目状态" prop="status">
              <Input v-model="previewForm.status" placeholder="" v-bind:readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目标识(是否为国民经济计划)" prop="is_gc">
              <Input v-model="previewForm.is_gc" placeholder="" v-bind:readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="国民经济计划分类" prop="nep_type">
              <Input v-model="previewForm.nep_type" placeholder="" v-bind:readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="计划开始时间" prop="plan_start_at">
              <DatePicker type="month" v-bind:readonly="isReadOnly" placeholder="" format="yyyy年MM月"
                          v-model="previewForm.plan_start_at"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="计划结束时间" prop="plan_end_at">
              <DatePicker type="month" v-bind:readonly="isReadOnly" placeholder=""
                          format="yyyy年MM月" v-model="previewForm.plan_end_at"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <FormItem label="项目中心点坐标" prop="center_point">
            <Input v-model="previewForm.center_point" placeholder="" v-bind:readonly="isReadOnly"/>
          </FormItem>
        </Row>
        <Row>
          <FormItem label="项目轮廓点坐标" prop="positions">
            <Input v-model="previewForm.positions" placeholder="" v-bind:readonly="isReadOnly"/>
          </FormItem>
        </Row>
        <Row>
          <FormItem label="项目概况" prop="description">
            <Input v-model="previewForm.description" type="textarea" :rows="4" placeholder=""
                   v-bind:readonly="isReadOnly"></Input>
          </FormItem>
        </Row>
        <Divider><h4>投资计划</h4></Divider>
        <div v-for="(item, index) in previewForm.projectPlan">
          <Divider orientation="left"><h5 style="color: #2d8cf0;">{{item.date}}年项目投资计划</h5></Divider>
          <Row>
            <Col span="12">
              <FormItem
                label="计划投资金额（万元）"
                :prop="'projectPlan.' + index + '.amount'">
                <Input v-model="item.amount" placeholder="" v-bind:readonly="isReadOnly"/>
              </FormItem>
            </Col>
            <Col span="12">
              <FormItem
                label="计划形象进度"
                :prop="'projectPlan.' + index + '.image_progress'">
                <Input v-model="item.image_progress" type="textarea" :rows="1" placeholder=""
                       v-bind:readonly="isReadOnly"></Input>
              </FormItem>
            </Col>
          </Row>
          <Row>
            <Col span="8">
              <Input type="text" value="月份" class="borderNone"/>
            </Col>
            <Col span="8">
              <Input type="text" value="计划投资金额(万元)" class="borderNone"/>
            </Col>
            <Col span="8">
              <Input type="text" value="计划形象进度" class="borderNone"/>
            </Col>
            <div v-for="(ite, index) in item.month">
              <Col span="8">
                <Input type="text" v-model="ite.date + '月'" readonly class="monthInput"/>
              </Col>
              <Col span="8">
                <Input type="text" placeholder="" v-model="ite.amount" readonly class="monthInput"/>
              </Col>
              <Col span="8">
                <Input type="text" placeholder="" v-model="ite.image_progress" readonly class="monthInput"/>
              </Col>
            </div>
          </Row>
        </div>
      </Form>
    </Modal>
  </Card>
</template>
<script>
  import {getAllWarning, getEditFormData} from '../../../api/project';
  import './projectEarlyWarning.css';

  export default {
    data() {
      return {
        data: [],
        previewForm: {
          title: '',
          num: '',
          subject: '',
          type: '',
          build_type: '',
          money_from: '',
          status: '',
          unit: '',
          amount: null,
          land_amount: null,
          is_gc: '',
          nep_type: '',
          plan_start_at: '',
          plan_end_at: '',
          center_point: '',
          description: '',
          positions: '',
          projectPlan: [
            {
              date: '2019',
              amount: null,
              image_progress: '',
              month: [
                {
                  date: 1,
                  amount: null,
                  image_progress: ''
                }
              ]
            },
          ],
        },
        projectPlan: [],
        previewModal: false,
        isReadOnly: false,
        index: 1,
        columns: [
          {
            title: '项目名称',
            key: 'title',
          },
          {
            title: '项目填报时间',
            key: 'schedule_at',
          },
          {
            title: '存在问题',
            key: 'problem',
          },
          {
            title: '预警类型',
            key: 'tags',
            render: (h, params) => {
              let button_rbg = 'success';
              let war_title = '已经超额';
              if (params.row.tags === 0) {
                button_rbg = 'success';
                war_title = '已经超额';
              } else if (params.row.tags === 1) {
                button_rbg = 'warning';
                war_title = '警告超额';
              } else if (params.row.tags === 2) {
                button_rbg = 'error';
                war_title = '严重超额';
              }
              return h("div", [
                h(
                  "Button",
                  {
                    props: {
                      type: button_rbg,
                      size: "small"
                    },
                    style: {
                      marginRight: "5px"
                    },
                  },
                  war_title
                )
              ]);
            }
          }, {
            title: '操作',
            key: 'action',
            render: (h, params) => {
              return h("div", [
                h('Button', {
                  props: {
                    type: 'primary',
                    size: 'small',
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      getEditFormData(params.row.project_id).then(res => {
                        this.previewForm = res.result;
                        this.isReadOnly = true;
                        this.previewModal = true;
                      });
                    }
                  }
                }, '查看详情')
              ]);
            }
          }],
        loadingTable: true,
        formId: '',
        project_list: []
      }
    },
    methods: {
      init() {
        this.$refs.previewFormValidate.resetFields();
        getAllWarning().then(res => {
          this.data = res.result;
          this.loadingTable = false;

        });
      },
    },
    mounted() {
      this.init();
    }
  }
</script>
