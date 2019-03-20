<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="70" class="search-form">
        <FormItem label="项目名称" prop="project_id">
          <Select v-model="searchForm.project_id" filterable style="width: 200px">
            <Option v-for="item in project_id" :value="item.id" :key="item.id">{{ item.title }}</Option>
          </Select>
        </FormItem>
        <Form-item label="项目编号" prop="project_num">
          <Input
            type="text"
            v-model="searchForm.project_num"
            placeholder="请输入项目编号"
            style="width: 200px"
          />
        </Form-item>
        <span v-if="drop">
          <Form-item label="投资主体" prop="subject">
            <Select v-model="searchForm.subject" filterable style="width: 200px">
              <Option v-for="item in data" :value="item.subject" :key="item.id">{{ item.subject }}</Option>
            </Select>
          </Form-item>
          <FormItem label="填报起止时间" prop="build_at">
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
        </span>
        <FormItem style="margin-left:-35px;" class="br">
          <Button @click="getProjectScheduleList" type="primary" icon="ios-search">搜索</Button>
           <Button @click="handleResetSearch">重置</Button>
          <a class="drop-down" @click="dropDown">
            {{dropDownContent}}
            <Icon :type="dropDownIcon"></Icon>
          </a>
        </FormItem>
      </Form>
    </Row>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add" v-if="isShowButton">填报</Button>
      <Button class="exportReport" @click="exportSchedule" type="primary" :disabled="btnDisable" icon="md-cloud-upload">导出台账
      </Button>
      <Button class="exportReport" @click="downloadPic" type="primary" :disabled="picDisable" icon="md-cloud-upload">下载形象进度照片
      </Button>
    </p>
    <Table type="selection" stripe border :columns="columns" :data="data" :loading="tableLoading"></Table>
    <Modal
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="项目进度填报">
      <Form ref="form" :model="form" :label-width="160" :rules="formValidate">
        <Row>
          <Col span="12">
            <FormItem label="填报项目" prop="project_id">
              <Select v-model="form.project_id" filterable @on-change="changeProject">
                <Option v-for="item in project_id" :value="item.id" :key="item.id">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="填报时间" prop="month">
              <DatePicker @on-change="changeMonth" type="month" :options="month_options_0" placeholder="请选择" format="yyyy-MM"
                          v-model="form.month"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目编号" prop="project_num">
              <Input v-model="form.project_num" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="form.subject" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="建设起止年限" prop="build_at">
              <Row>
                <Col span="11">
                  <DatePicker type="month" placeholder="开始时间" format="yyyy-MM" readonly
                              v-model="form.build_start_at"></DatePicker>
                </Col>
                <Col span="2" style="text-align: center">-</Col>
                <Col span="11">
                  <DatePicker type="month" placeholder="结束时间" format="yyyy-MM" readonly
                              v-model="form.build_end_at"></DatePicker>
                </Col>
              </Row>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="总投资" prop="total_investors">
              <Input v-model="form.total_investors" placeholder="万元" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem :label="year_investors" prop="plan_investors">
              <Input v-model="form.plan_investors" placeholder="万元" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem :label="year_img" prop="plan_img_progress">
              <Input type="textarea" v-model="form.plan_img_progress" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Divider />
        <Row>
          <Col span="12">
            <FormItem :label="month_act"  prop="month_act_complete">
              <Input @on-blur="changeMonthActComplete" v-model="form.month_act_complete" placeholder="万元"></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计完成投资" prop="acc_complete">
              <Input v-model="form.acc_complete" placeholder="万元" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem :label="month_img" prop="month_img_progress">
              <Input type="textarea" v-model="form.month_img_progress" placeholder=""/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计形象进度" prop="acc_img_progress">
              <Input type="textarea" v-model="form.acc_img_progress" placeholder=""/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="计划开工时间" prop="plan_build_start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                          v-model="form.plan_build_start_at"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
              <Input v-model="form.exp_preforma" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="form.problem" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..."></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="备注" prop="marker">
              <Input v-model="form.marker" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
            <FormItem label="形象进度" prop="img_progress_pic">
              <Upload
                ref="upload"
                :disabled="upbtnDisabled"
                name="img_pic"
                :on-success="handleSuccess"
                multiple
                :data="upData"
                :format="['jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'pdf']"
                :max-size="5120"
                action="/api/project/uploadPic">
                <Button icon="ios-cloud-upload-outline">上传</Button>
              </Upload>
            </FormItem>
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
    <!-- 查看model------------------------------------------------------------------------------ -->
    <Modal
      v-model="seeModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="查看项目进度信息">
      <Form ref="seeForm" :model="seeForm" :label-width="150">
        <Row>
          <Col span="12">
            <FormItem label="填报项目" prop="project_id">
              <Input v-model="seeForm.project_id" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="填报月份" prop="month">
              <DatePicker type="month" placeholder="" format="yyyy-MM" v-model="seeForm.month" readonly>
              </DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目编号" prop="project_num">
              <Input v-model="seeForm.project_num" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="seeForm.subject" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="建设起止年限" prop="build_at">
              <Row>
                <Col span="11">
                  <DatePicker type="month" placeholder="" format="yyyy-MM"
                              v-model="seeForm.build_start_at" readonly></DatePicker>
                </Col>
                <Col span="2" style="text-align: center">-</Col>
                <Col span="11">
                  <DatePicker type="month" placeholder="" format="yyyy-MM"
                              v-model="seeForm.build_end_at" readonly></DatePicker>
                </Col>
              </Row>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="总投资" prop="total_investors">
              <Input v-model="seeForm.total_investors" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem :label="year_investors" prop="plan_investors">
              <Input v-model="seeForm.plan_investors" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem :label="year_img" prop="plan_img_progress">
              <Input v-model="seeForm.plan_img_progress" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Divider />
        <Row>
          <Col span="12">
            <FormItem :label="month_act" prop="month_act_complete">
              <Input v-model="seeForm.month_act_complete" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计完成投资" prop="acc_complete">
              <Input v-model="seeForm.acc_complete" placeholder="" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem :label="month_img" prop="month_img_progress">
              <Input type="textarea" v-model="seeForm.month_img_progress" placeholder="" readonly/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计形象进度" prop="acc_img_progress">
              <Input type="textarea" v-model="seeForm.acc_img_progress" placeholder="" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="计划开工时间" prop="plan_build_start_at">
              <DatePicker type="month" placeholder="" format="yyyy-MM"
                          v-model="seeForm.plan_build_start_at" readonly></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
              <Input v-model="seeForm.exp_preforma" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..." readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="seeForm.problem" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="请输入.."
                     readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="备注" prop="marker">
              <Input v-model="seeForm.marker" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..." readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24" style="margin-left: 20px;">
            <div class="demo-upload-list" v-for="item in defaultList">
              <template>
                <img :src="item.url">
                <div class="demo-upload-list-cover">
                  <Icon type="ios-eye-outline" @click.native="handleView(item.url)"></Icon>
                </div>
              </template>
              <template>
                <Progress hide-info></Progress>
              </template>
            </div>
            <Modal title="查看照片" v-model="visible">
              <img :src="imgUrl" style="width: 100%">
            </Modal>
          </Col>
        </Row>
      </Form>
      <div slot="footer">
        <Dropdown trigger="click" style="margin-left: 20px" @on-click="audit" v-if="showAuditButton">
          <Button type="primary">
            审核
            <Icon type="ios-arrow-down"></Icon>
          </Button>
          <DropdownMenu slot="list">
            <DropdownItem name="1">审核通过</DropdownItem>
            <DropdownItem name="2" style="color: #ed4014">审核不通过</DropdownItem>
          </DropdownMenu>
        </Dropdown>
      </div>
    </Modal>
    <!-- 编辑model------------------------------------------------------------------------------ -->
    <Modal
      v-model="editModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="编辑项目进度">
      <Form ref="editForm" :model="editForm" :label-width="150" :rules="formValidate">
        <Row>
          <Col span="12">
            <FormItem label="填报项目" prop="">
              <Input v-model="editForm.project_id" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="填报月份" prop="">
              <DatePicker @on-change="changeMonth" type="month" placeholder="填报月份" format="yyyy-MM"
                          v-model="editForm.month" readonly></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目编号" prop="project_num">
              <Input v-model="editForm.project_num" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="editForm.subject" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="建设起止年限" prop="build_at">
              <Row>
                <Col span="11">
                  <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                              v-model="editForm.build_start_at" readonly></DatePicker>
                </Col>
                <Col span="2" style="text-align: center">-</Col>
                <Col span="11">
                  <DatePicker type="month" placeholder="结束时间" format="yyyy-MM"
                              v-model="editForm.build_end_at" readonly></DatePicker>
                </Col>
              </Row>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="总投资" prop="total_investors">
              <Input v-model="editForm.total_investors" placeholder="万元"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem :label="year_investors" prop="plan_investors">
              <Input v-model="editForm.plan_investors" placeholder="万元" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem :label="year_img" prop="plan_img_progress">
              <Input type="textarea" v-model="editForm.plan_img_progress" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Divider />
        <Row>
          <Col span="12">
            <FormItem :label="month_act" prop="month_act_complete">
              <Input v-model="editForm.month_act_complete" @on-blur="changeMonthActComplete" placeholder="万元"></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计完成投资" prop="acc_complete">
              <Input v-model="editForm.acc_complete" placeholder="万元" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem :label="month_img" prop="month_img_progress">
              <Input type="textarea" v-model="editForm.month_img_progress" placeholder=""/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计形象进度" prop="acc_img_progress">
              <Input type="textarea" v-model="editForm.acc_img_progress" placeholder=""/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="计划开工时间" prop="plan_build_start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                          v-model="editForm.plan_build_start_at" readonly></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
              <Input v-model="editForm.exp_preforma" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="editForm.problem" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..."></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="备注" prop="marker">
              <Input v-model="editForm.marker" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24" style="margin-left: 20px;">
            <div class="demo-upload-list" v-for="item in defaultList">
              <template>
                <img :src="item.url">
                <div class="demo-upload-list-cover">
                  <Icon type="ios-eye-outline" @click.native="handleView(item.url)"></Icon>
                </div>
              </template>
              <template>
                <Progress hide-info></Progress>
              </template>
            </div>
            <Modal title="查看照片" v-model="visible">
              <img :src="imgUrl" style="width: 100%">
            </Modal>
          </Col>
        </Row>
      </Form>
      <div slot="footer">
        
        <Button
          @click="editSubmit('editForm')"
          :loading="submitLoading"
          type="primary"
          icon="ios-create-outline"
          style="margin-left:8px"
        >保存
        </Button>
        <Button @click="handleReset('editform')" :loading="loading">重置</Button>
      </div>
    </Modal>
  </Card>
</template>
<script>
  import {
    initProjectInfo,
    projectProgress,
    projectProgressList,
    projectPlanInfo,
    editProjectProgress,
    getData,
    auditProjectProgress,
    toAuditSchedule,
    actCompleteMoney
  } from '../../../api/project';
  import './projectSchedule.css'

  export default {
    data() {
      return {
        isShowButton: true,
        showAuditButton: false,
        formId: '',
        dropDownContent: '展开',
        drop: false,
        dropDownIcon: "ios-arrow-down",
        btnDisable: true,
        upbtnDisabled:true,
        picDisable:true,
        searchForm: {
          project_id: '',
          project_num: '',
          subject: '',
          start_at: '',
          end_at: ''
        },
        seeModal: false,
        editModal: false,
        columns: [
          {
            title: '项目名称',
            key: 'project_id',
            width: 250,
            fixed: 'left'
          },
          {
            title: '填报月份',
            key: 'month',
            width: 100
          },
          {
            title: '项目编号',
            key: 'project_num',
            width: 100
          },
          {
            title: '投资主体',
            key: 'subject',
            width: 100
          },
          {
            title: '建设开始年限',
            key: 'build_start_at',
            width: 120
          },
          {
            title: '建设结束年限',
            key: 'build_end_at',
            width: 120
          },
          {
            title: '总投资(万元)',
            key: 'total_investors',
            width: 120,
            align: "right"
          },
          {
            title: '年计划投资金额(万元)',
            key: 'plan_investors',
            width: 160,
            align: "right"
          },
          {
            title: '年计划形象进度',
            key: 'plan_img_progress',
            width: 250
          },
          {
            title: '月实际完成投资(万元)',
            key: 'month_act_complete',
            width: 170,
            align: "right"
          },
          {
            title: '月形象进度',
            key: 'month_img_progress',
            width: 250
          },
          {
            title: '累计完成投资(万元)',
            key: 'acc_complete',
            width: 150,
            align: "right"
          },
          {
            title: '累计形象进度',
            key: 'acc_img_progress',
            width: 250
          },
          {
            title: '存在问题',
            key: 'problem',
            width: 250
          },
          {
            title: '计划开工时间',
            key: 'plan_build_start_at',
            width: 120
          },
          {
            title: '审核状态',
            key: 'is_audit',
            fixed: 'right',
            width: 160,
            render: (h, params) => {
              let edit_button = '';
              if (params.row.is_audit === 4) {
                edit_button = h('Button', {
                  props: {
                    type: 'primary',
                    size: 'small'
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      this.goToAudit(params.row);
                    }
                  }
                }, '提交审核');
              } else {
                const row = params.row;
                const color = row.is_audit === 0 ? 'warning' : row.is_audit === 1 ? 'success' : row.is_audit === 2 ? 'error' : 'primary';
                const text = row.is_audit === 0 ? '待审核' : row.is_audit === 1 ? '审核通过' : row.is_audit === 2 ? '审核不通过' : '投资调整';
                edit_button = h('Tag', {
                  props: {
                    type: 'dot',
                    color: color
                  }
                }, text);
              }
              return edit_button;
            }
          },
          {
            title: '操作',
            key: 'action',
            width: 150,
            fixed: 'right',
            align: 'center',
            render: (h, params) => {
              let editButton;
              const groupId = this.$store.getters.user.group_id;
              if (groupId === 6) {
                editButton = !(params.row.is_audit === 3 || params.row.is_audit === 2 || params.row.is_audit === 4);
              }
              if (groupId === 4 || groupId === 7) {
                editButton = false;
              }
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
                      this.month_img = params.row.month + ' 月形象进度';
                      this.month_act = params.row.month + ' 月实际完成投资';
                      this.year_investors = params.row.month.substring(0, 4) + '年计划投资';
                      this.year_img = params.row.month.substring(0, 4) + '年形象进度';
                      const groupId = this.$store.getters.user.group_id;
                      if (groupId === 6) {
                        this.showAuditButton = false;
                      }
                      if (groupId === 4 || groupId === 7) {
                        this.showAuditButton = params.row.is_audit === 0;
                      }
                      this.formId = params.row.id;
                      let _seeThis = this;
                      this.data.forEach(function (em) {
                        if (em.id === params.row.id) {
                          _seeThis.seeForm.month = em.month;
                          let em_project_id = em.project_id;
                          _seeThis.project_id.forEach(function (em_id) {
                            if (em_project_id === em_id.title) {
                              _seeThis.seeForm.project_id = em_id.title;
                            }
                          });
                          _seeThis.seeForm.project_num = em.project_num;
                          _seeThis.seeForm.subject = em.subject;
                          _seeThis.seeForm.build_start_at = em.build_start_at;
                          _seeThis.seeForm.build_end_at = em.build_end_at;
                          _seeThis.seeForm.total_investors = em.total_investors;
                          _seeThis.seeForm.plan_investors = em.plan_investors;
                          _seeThis.seeForm.plan_img_progress = em.plan_img_progress;
                          _seeThis.seeForm.month_img_progress = em.month_img_progress;
                          _seeThis.seeForm.month_act_complete = em.month_act_complete;
                          _seeThis.seeForm.acc_img_progress = em.acc_img_progress;
                          _seeThis.seeForm.acc_complete = em.acc_complete;
                          _seeThis.seeForm.problem = em.problem;
                          _seeThis.seeForm.plan_build_start_at = em.plan_build_start_at;
                          _seeThis.seeForm.exp_preforma = em.exp_preforma;

                          let img_pic = [];
                          if (em.img_progress_pic) {
                            let pics = em.img_progress_pic.split(",");
                            let i = 0;
                            pics.forEach(function (em_pic) {
                              img_pic.push({url: em_pic, name: i});
                              i++;
                            })
                          }
                          _seeThis.defaultList = img_pic;
                          _seeThis.seeForm.marker = em.marker;
                        }
                      });
                      this.seeModal = true;
                    }
                  }
                }, '查看'),
                h('Button', {
                  props: {
                    type: 'primary',
                    size: 'small',
                    disabled: editButton,
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      this.month_img = params.row.month + ' 月形象进度';
                      this.month_act = params.row.month + ' 月实际完成投资';
                      this.year_investors = params.row.month.substring(0, 4) + '年计划投资';
                      this.year_img = params.row.month.substring(0, 4) + '年形象进度';
                      this.editForm.id = params.row.id;
                      let _editThis = this;
                      this.data.forEach(function (em) {
                        if (em.id === params.row.id) {
                          _editThis.editForm.month = em.month;
                          let em_project_id = em.project_id;
                          _editThis.project_id.forEach(function (em_id) {
                            if (em_project_id === em_id.title) {
                              _editThis.editForm.project_id = em_id.title;
                            }
                          });
                          _editThis.editForm.project_num = em.project_num;
                          _editThis.editForm.subject = em.subject;
                          _editThis.editForm.build_start_at = em.build_start_at;
                          _editThis.editForm.build_end_at = em.build_end_at;
                          _editThis.editForm.total_investors = em.total_investors;
                          _editThis.editForm.plan_investors = em.plan_investors;
                          _editThis.editForm.plan_img_progress = em.plan_img_progress;
                          _editThis.editForm.month_img_progress = em.month_img_progress;
                          _editThis.editForm.month_act_complete = em.month_act_complete;
                          _editThis.editForm.acc_img_progress = em.acc_img_progress;
                          _editThis.editForm.acc_complete = em.acc_complete;
                          _editThis.editForm.problem = em.problem;
                          _editThis.editForm.plan_build_start_at = em.plan_build_start_at;
                          _editThis.editForm.exp_preforma = em.exp_preforma;

                          let img_pic = [];
                          if (em.img_progress_pic) {
                            let pics = em.img_progress_pic.split(",");
                            let i = 0;
                            pics.forEach(function (em_pic) {
                              img_pic.push({url: em_pic, name: i});
                              i++;
                            })
                          }
                          _editThis.defaultList = img_pic;
                          _editThis.editForm.marker = em.marker;
                        }
                      });
                      this.editModal = true;
                    }
                  }
                }, '编辑')
              ]);
            }
          }
        ],
        data: [],
        tableLoading: true,
        loading: false,
        month_img: '-月形象进度',
        month_act: '-月实际完成投资',
        year_investors: '--年计划投资',
        year_img: '--年形象进度',
        form: {
          month: '',
          project_id: '',
          project_num: '',
          subject: '',
          build_start_at: '',
          build_end_at: '',
          total_investors: '',
          plan_investors: '',
          plan_img_progress: '',
          month_act_complete: '',
          month_img_progress: '',
          acc_complete: '',
          acc_img_progress: '',
          problem: '',
          plan_build_start_at: '',
          exp_preforma: '',
          img_progress_pic: '',
          marker: '',
          is_audit: ''
        },
        editForm: {},
        seeForm: {},
        index: 1,
        modal: false,
        submitLoading: false,
        formValidate: {
          // 表单验证规则
          month: [
            {required: true, type: 'date', message: "填报月份不能为空", trigger: "change"}
          ],
          project_id: [
            {required: true, message: '项目名称不能为空', trigger: 'change', type: 'number'}
          ],
          month_act_complete: [
            {required: true, message: '月底即完成投资不能为空', trigger: 'blur'}
          ],
          // acc_complete: [
          //   {required: true, message: '累计完成投资不能为空', trigger: 'blur'}
          // ],
        },
        project_id: [],
        imgName: '',
        visible: false,
        uploadList: [],
        imgUrl: '',
        defaultList: [],
        is_audit: [],
        editButton: false,
        upData:{},
        month_options_0:{
          disabledDate (date) {
            let date_at = new Date();
            const disabledMonth = date.getMonth();
            return disabledMonth < date_at.getMonth();;
          }
        },
        scheduleMonth:[]
      }
    },
    methods: {
      init() {
        this.month_im = '-月形象进度';
        this.month_ac = '-月实际完成投资';
        this.year_investor = '--年计划投资';
        this.year_im = '--年形象进度';
        const groupId = this.$store.getters.user.group_id;
        if (groupId === 6) {
          this.showAuditButton = false;
          this.editButton = false;
          this.isShowButton = true;
        }
        if (groupId === 4 || groupId === 7) {
          this.showAuditButton = true;
          this.editButton = true;
          this.isShowButton = false;
        }
        this.$refs.form.resetFields();// 获取项目名称
        this.getProjectId();
        this.getProjectScheduleList();
      },
      getProjectScheduleList() {
        if(this.searchForm.project_id||this.searchForm.project_num||this.searchForm.subject||this.searchForm.start_at||this.searchForm.end_at){
          this.btnDisable=false;
        }
        this.tableLoading = true;
        projectProgressList(this.searchForm).then(res => {
          this.data = res.result;
          this.tableLoading = false;
        });
      },
      audit(name) {
        auditProjectProgress({id: this.formId, status: name}).then(res => {
          if (res.result) {
            this.seeModal = false;
            this.$Message.success('审核状态修改成功!');
            this.getProjectScheduleList();
          }
        });
      },
      goToAudit(row) {
        toAuditSchedule(row.id).then(res => {
          if (res.result) {
            this.$Message.success('提交成功!');
            this.init();
          }
        });
      },
      getProjectId() {
        initProjectInfo().then(res => {
          this.project_id = res.result;
        });
      },
      getIsAudit() {
        getData({title: '审核状态'}).then(res => {
          this.is_audit = res.result;
        });
      },
      changeProject(e) {
        this.project_id.forEach((em) => {
          if (em.id === e) {
            this.form.subject = em.subject;
            this.form.project_num = em.num;
            this.form.build_start_at = em.plan_start_at;
            this.form.build_end_at = em.plan_end_at;
            this.form.total_investors = em.amount;
            this.form.plan_img_progress = em.image_progress;
          }
        });
      },
      changeMonth(e) {
        if (this.form.project_id === '') {
          this.$Message.error('请先选择填报项目!');
          this.form.month = '';
          return;
        }
        this.upbtnDisabled=false;
        let month_time = new Date(this.form.month); 
        let month_time_0=(month_time.getMonth() + 1)>9?(month_time.getMonth() + 1):'0'+(month_time.getMonth() + 1);
        
        month_time=month_time.getFullYear() + '-' + month_time_0;
        this.upData={month:month_time,project_num:this.form.project_num,project_id:this.form.project_id};
        this.month_img = e + ' 月形象进度';
        this.month_act = e + ' 月实际完成投资';
        this.year_investors = e.substring(0, 4) + '年计划投资';
        this.year_img = e.substring(0, 4) + '年形象进度';
        if (this.form.project_id) {
          projectPlanInfo({month:this.form.month,project_id:this.form.project_id}).then(res => {
            this.form.plan_investors = res.result.amount;
            this.form.plan_img_progress = res.result.image_progress;
          });
        }
      },// 月实际完成投资发生改变时 改变累计投资
      changeMonthActComplete(e){
        if (this.form.project_id === '') {
          this.$Message.error('请先选择填报项目!');
          this.form.month_act_complete = '';
          return;
        }
        if (this.form.month === '') {
          this.$Message.error('请先选择填报时间!');
          this.form.month_act_complete = '';
          return;
        }       
        actCompleteMoney({month:this.form.month,project_id:this.form.project_id}).then(res => {
          this.form.acc_complete = parseFloat(res.result)+parseFloat(this.form.month_act_complete);
        });
      },
      handleReset(name) {
        this.$refs[name].resetFields();
        this.$refs.upload.clearFiles();
      },
      handleClearFiles () {
        this.$refs.upload.clearFiles();
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            projectProgress(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("填报成功");
                this.modal = false;
                this.init();
              } else {
                this.$Message.error('填报失败!');
              }
            });
          }
        })
      },
      editSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            editProjectProgress(this.editForm).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("修改成功");
                this.handleClearFiles();
                this.editModal = false;
                this.init();
              } else {
                this.$Message.error('修改失败!');
              }
            });
          }
        })
      },
      cancel() {
        this.$refs.form.resetFields();
        this.handleClearFiles();
        this.month_im = '-月形象进度';
        this.month_ac = '-月实际完成投资';
        this.year_investor = '--年计划投资';
        this.year_im = '--年形象进度';
      },//上传图片
      handleView(url) {
        this.imgUrl = url;
        this.visible = true;
      },
      handleSuccess(res, file) {
        this.form.img_progress_pic = this.form.img_progress_pic + ',' + res.result;
      },
      handleFormatError(file) {
        this.$Notice.warning({
          title: '文件格式不正确',
          desc: '文件格式不正确，请选择JPG或PNG'
        });
      },
      handleMaxSize(file) {
        this.$Notice.warning({
          title: '超出文件大小限制',
          desc: '文件太大，不能超过5M'
        });
      },
      handleBeforeUpload() {
        const check = this.uploadList.length < 5;
        if (!check) {
          this.$Notice.warning({
            title: '最多可上载5张图片'
          });
        }
        return check;
      },
      handleResetSearch() {
        this.searchForm = {
          project_id: '',
          project_num: '',
          subject: '',
          start_at: '',
          end_at: ''
        };
        this.getProjectScheduleList();
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
      },//导出
      exportSchedule(){
        this.picDisable=false;
        let project_id = this.searchForm.project_id;
        let project_num = this.searchForm.project_num;
        let subject = this.searchForm.subject;
        let start_at = this.searchForm.start_at;
        let end_at = this.searchForm.end_at;
        let start_time='';
        if(start_at){
          let start_time_0 = new Date(start_at);  
          let month_start_time_0=(start_time_0.getMonth() + 1)>9?(start_time_0.getMonth() + 1):'0'+(start_time_0.getMonth() + 1);
          start_time=start_time.getFullYear() + '-' + month_start_time_0; 
        }
        let end_time='';
        if(end_at){
          let end_time_0 = new Date(end_at); 
          let month_end_time_0=(end_time_0.getMonth() + 1)>9?(end_time_0.getMonth() + 1):'0'+(end_time_0.getMonth() + 1);
          end_time=end_time.getFullYear() + '-' + month_end_time_0;
        };         
        window.location.href="/api/project/exportSchedule?project_id="+project_id+"&project_num="+project_num+"&subject="+subject+"&start_at="+start_time+"&end_at="+end_time;
      },//下载
      downloadPic(){
        let project_id = this.searchForm.project_id;
        let project_num = this.searchForm.project_num;
        let subject = this.searchForm.subject;
        let start_at = this.searchForm.start_at;
        let end_at = this.searchForm.end_at;
        let start_time='';
        if(start_at){
          let start_time_0 = new Date(start_at);  
          let month_start_time_0=(start_time_0.getMonth() + 1)>9?(start_time_0.getMonth() + 1):'0'+(start_time_0.getMonth() + 1);
          start_time=start_time.getFullYear() + '-' + month_start_time_0; 
        }
        let end_time='';
        if(end_at){
          let end_time_0 = new Date(end_at); 
          let month_end_time_0=(end_time_0.getMonth() + 1)>9?(end_time_0.getMonth() + 1):'0'+(end_time_0.getMonth() + 1);
          end_time=end_time.getFullYear() + '-' + month_end_time_0;
        };         
        window.location.href="/api/project/downLoadSchedule?project_id="+project_id+"&project_num="+project_num+"&subject="+subject+"&start_at="+start_time+"&end_at="+end_time;
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
<style scoped>
  .ivu-divider-horizontal {
    margin: 0 0 24px 0 !important;
  }
</style>
