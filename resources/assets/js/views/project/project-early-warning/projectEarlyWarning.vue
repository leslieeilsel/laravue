<template>
  <Card>
    <Alert type="warning" class="description">
      <Row>
        <Button type="warning" size="small">警告滞后</Button>
        <span>：70% ≤ 完成率 < 100%</span>
      </Row>
      <Row>
        <Button type="error" size="small">严重滞后</Button>
        <span>：完成率 < 70%</span>
      </Row>
    </Alert>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="90" class="search-form">
        <Form-item label="预警类型" prop="warning_type">
          <Select v-model="searchForm.warning_type" style="width: 200px">
            <Option value='-1' key='-1'>全部</Option>
            <Option v-for="item in warnings" :value="item.value" :key="item.value">{{ item.name }}</Option>
          </Select>
        </Form-item>
        <FormItem label="填报起止时间" prop="schedule_at">
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
        </FormItem>
        <Form-item style="margin-left:-70px;" class="br">
          <Button @click="getWarnings" type="primary" icon="ios-search">搜索</Button>
          <Button @click="handleResetSearch">重置</Button>
        </Form-item>
      </Form>
    </Row>
    <Table border :columns="columns" :data="data" :loading="loadingTable"></Table>
    <Row type="flex" justify="end" class="page">
      <Page
        :total="dataCount"
        :page-size="searchForm.pageSize"
        @on-change="changePage"
        @on-page-size-change="changePageSize"
        show-total
        :current="searchForm.pageNumber"/>
    </Row>
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
      <div slot="footer">
      </div>
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
        warnings: [
          {
            name: '警告滞后',
            value: 1
          },
          {
            name: '严重滞后',
            value: 2
          },
        ],
        searchForm: {
          warning_type: '',
          start_at: '',
          end_at: '',
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        dataCount: 0,   // 总条数
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
            type: 'index2',
            width: 60,
            align: 'center',
            fixed: 'left',
            render: (h, params) => {
              return h('span', params.index + (this.searchForm.pageNumber - 1) * this.searchForm.pageSize + 1);
            }
          },
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
              let button_rbg = '';
              let war_title = '';

              if (params.row.tags === 1) {
                button_rbg = 'warning';
                war_title = '警告滞后';
              } else if (params.row.tags === 2) {
                button_rbg = 'error';
                war_title = '严重滞后';
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
        this.getWarnings();
      },
      getWarnings() {
        this.loadingTable = true;
        getAllWarning(this.searchForm).then(res => {
          this.loadingTable = false;
          this.data = res.result;
          this.dataCount = res.total;
        });
      },
      handleResetSearch() {
        this.searchForm = {
          warning_type: '',
          start_at: '',
          end_at: '',
        };
        this.dataCount = 0;
        this.searchForm.pageNumber = 1;
        this.searchForm.pageSize = 10;
        this.getWarnings();
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getWarnings();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getWarnings();
      },
    },
    mounted() {
      this.init();
    }
  }
</script>
<style>
  .description .ivu-btn,
  .ivu-table-cell .ivu-btn-success,
  .ivu-table-cell .ivu-btn-warning,
  .ivu-table-cell .ivu-btn-error {
    cursor: auto;
  }
</style>