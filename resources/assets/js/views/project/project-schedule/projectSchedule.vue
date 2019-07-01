<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="90" class="search-form">
        <FormItem label="项目名称" prop="title">
          <Input clearable v-model="searchForm.title" placeholder="支持模糊搜索" style="width: 200px"/>
        </FormItem>
        <FormItem label="填报时间" prop="build_at">
          <Row style="width: 110px">
            <!-- <Col span="11">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM" v-model="searchForm.start_at">
              </DatePicker>
            </Col>
            <Col span="2" style="text-align: center">-</Col> -->
            <Col span="24">
              <DatePicker type="month" placeholder="填报时间" format="yyyy-MM" v-model="searchForm.end_at">
              </DatePicker>
            </Col>
          </Row>
        </FormItem>
        <span v-if="drop">
          <Form-item label="项目编号" prop="project_num">
            <Input v-model="searchForm.project_num" placeholder="请输入项目编号" style="width: 200px"/>
          </Form-item>
          <Form-item label="投资主体" prop="subject">
            <Input clearable v-model="searchForm.subject" placeholder="支持模糊搜索" style="width: 200px"/>
          </Form-item>
        <FormItem label="部门" prop="department_id">
          <Cascader :data="dataDep1" v-model="searchForm.department_id" placeholder="选择部门" trigger="hover"
                    style="width: 200px" :render-format="format"></Cascader>
          <!-- <Poptip trigger="click" placement="right" title="选择部门" width="340">
            <div style="display:flex;">
              <Input v-model="searchForm.department_title" readonly style="width: 200px;" placeholder=""/>
            </div>
            <div slot="content" class="tree-bar">
              <Tree :data="dataDep" :load-data="loadDataTree" @on-select-change="selectTreeS"></Tree>
              <Spin size="large" fix v-if="dpLoading"></Spin>
            </div>
          </Poptip> -->
        </FormItem>
          <FormItem label="资金来源">
            <Select v-model="searchForm.money_from" prop="money_from" style="width: 200px">
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.money_from" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="项目标识" prop="is_gc">
            <Select @on-change="onSearchIsGcChange" v-model="searchForm.is_gc" style="width: 200px"
                    placeholder="是否为国民经济计划">
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.is_gc" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
          <FormItem label="国民经济计划分类" prop="nep_type">
            <Select v-model="searchForm.nep_type" style="width: 200px" :disabled="searchNepDisabled">
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.nep_type" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
        </span>
        <FormItem style="margin-left:-70px;" class="br">
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
      <Button type="primary" @click="noSchedule = true" v-if="noScheduleButton">查看当月未填报项目</Button>
      <Button class="exportReport" @click="exportSchedule" type="primary" :disabled="btnDisable" icon="md-cloud-upload">
        导出进度
      </Button>
      <Button class="exportReport" @click="downloadPic" type="primary" :disabled="picDisable" icon="md-cloud-upload">
        下载形象进度照片
      </Button>
    </p>
    <Table type="selection" stripe border :columns="columns" :data="nowData" :loading="tableLoading"></Table>
    <Row type="flex" justify="end" class="page">
      <Page :total="dataCount" :page-size="pageSize" @on-change="changePage" @on-page-size-change="_nowPageSize"
            show-total show-sizer :current="pageCurrent"/>
    </Row>
    <Modal
      :mask-closable="false"
      v-model="noSchedule"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="750"
      title="项目进度当月没填报项目">
      <Table type="selection" stripe border :columns="scheduleColumns" :data="scheduleData" :loading="tableLoading">
      </Table>
    </Modal>
    <Modal
      :mask-closable="false"
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
              <DatePicker @on-change="changeMonth" type="month" :options="month_options_0" placeholder="请选择"
                          format="yyyy-MM"
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
            <FormItem label="总投资(万元)" prop="total_investors">
              <Input v-model="form.total_investors" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <FormItem :label="year_investors" prop="plan_investors">
              <Input v-model="form.plan_investors" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem :label="year_img" prop="plan_img_progress">
              <Input type="textarea" :rows="2" v-model="form.plan_img_progress" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Divider/>
        <Row>
          <Col span="12">
            <FormItem :label="month_act" prop="month_act_complete">
              <InputNumber @on-blur="changeMonthActComplete" :min="0" :step="1.2" v-model="form.month_act_complete"
                           placeholder="必填项"></InputNumber>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计完成投资(万元)" prop="acc_complete">
              <Input v-model="form.acc_complete" placeholder="" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem :label="month_img" prop="month_img_progress">
              <Input type="textarea" :rows="3" v-model="form.month_img_progress" placeholder="请输入..."/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计形象进度" prop="acc_img_progress">
              <Input type="textarea" :rows="3" v-model="form.acc_img_progress" placeholder="请输入..."/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="计划开工时间" prop="plan_build_start_at">
              <DatePicker type="month" placeholder="请选择" format="yyyy-MM"
                          v-model="form.plan_build_start_at" readonly></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
              <Input v-model="form.exp_preforma" type="textarea" :rows="3" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="form.problem" type="textarea" :rows="3" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="整改措施" prop="measures">
              <Input v-model="form.measures" type="textarea" :rows="3" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <FormItem label="备注" prop="marker">
              <Input v-model="form.marker" type="textarea" :rows="3" placeholder="请输入..."></Input>
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
              :max-size="600"
              :on-format-error="handleFormatError"
              :on-exceeded-size="handleMaxSize"
              action="/api/project/uploadPic">
              <Button icon="ios-cloud-upload-outline">上传</Button>
              <div style="color:#ea856b">文件大小不能超过600KB,请确保上传完毕之后再提交保存</div>
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
      :mask-closable="false"
      v-model="seeModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="查看项目进度信息">
      <Alert type="error" show-icon v-if="openErrorAlert">
        审核意见
        <span slot="desc">
          {{seeForm.reason}}
        </span>
      </Alert>
      <Form ref="seeForm" :model="seeForm" :label-width="150">
        <Row>
          <Col span="12">
            <FormItem label="填报项目" prop="project_id">
              <Input v-model="seeForm.project_title" placeholder="" readonly></Input>
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
            <FormItem label="总投资(万元)" prop="total_investors">
              <Input v-model="seeForm.total_investors" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <FormItem :label="year_investors" prop="plan_investors">
              <Input v-model="seeForm.plan_investors" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem :label="year_img" prop="plan_img_progress">
              <Input type="textarea" :rows="2" v-model="seeForm.plan_img_progress" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Divider/>
        <Row>
          <Col span="12">
            <FormItem :label="month_act" prop="month_act_complete">
              <Input v-model="seeForm.month_act_complete" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计完成投资(万元)" prop="acc_complete">
              <Input v-model="seeForm.acc_complete" placeholder="" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem :label="month_img" prop="month_img_progress">
              <Input type="textarea" :rows="3" v-model="seeForm.month_img_progress" placeholder="" readonly/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计形象进度" prop="acc_img_progress">
              <Input type="textarea" :rows="3" v-model="seeForm.acc_img_progress" placeholder="" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="计划开工时间" prop="plan_build_start_at">
              <Input v-model="seeForm.plan_build_start_at" placeholder="" readonly/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
              <Input v-model="seeForm.exp_preforma" type="textarea" :rows="3" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="seeForm.problem" type="textarea" :rows="3" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="整改措施" prop="measures">
              <Input v-model="seeForm.measures" type="textarea" :rows="3" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <FormItem label="备注" prop="marker">
              <Input v-model="seeForm.marker" type="textarea" :rows="3" placeholder="" readonly></Input>
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
      :mask-closable="false"
      v-model="editModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="编辑项目进度">
      <Alert type="error" show-icon v-if="openErrorAlert">
        审核意见
        <span slot="desc">
          {{editForm.reason}}
        </span>
      </Alert>
      <Form ref="editForm" :model="editForm" :label-width="150" :rules="formValidate">
        <Row>
          <Col span="12">
            <FormItem label="填报项目" prop="">
              <Input v-model="editForm.project_title" placeholder="" readonly></Input>
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
              <Input v-model="editForm.project_num" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="editForm.subject" placeholder="" readonly></Input>
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
            <FormItem label="总投资(万元)" prop="total_investors">
              <Input v-model="editForm.total_investors" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <FormItem :label="year_investors" prop="plan_investors">
              <Input v-model="editForm.plan_investors" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem :label="year_img" prop="plan_img_progress">
              <Input type="textarea" :rows="2" v-model="editForm.plan_img_progress" placeholder="请输入..."
                     readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Divider/>
        <Row>
          <Col span="12">
            <FormItem :label="month_act" prop="month_act_complete">
              <InputNumber @on-blur="changeEditMonthActComplete" :min="0" :step="1.2"
                           v-model="editForm.month_act_complete" placeholder="必填项"></InputNumber>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计完成投资(万元)" prop="acc_complete">
              <Input v-model="editForm.acc_complete" placeholder="" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem :label="month_img" prop="month_img_progress">
              <Input type="textarea" :rows="3" v-model="editForm.month_img_progress" placeholder="请输入..."/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="累计形象进度" prop="acc_img_progress">
              <Input type="textarea" :rows="3" v-model="editForm.acc_img_progress" placeholder="请输入..."/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="计划开工时间" prop="plan_build_start_at">
              <DatePicker type="month" placeholder="请选择" format="yyyy-MM" v-model="editForm.plan_build_start_at">
              </DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
              <Input v-model="editForm.exp_preforma" type="textarea" :rows="3" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="editForm.problem" type="textarea" :rows="3" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="整改措施" prop="measures">
              <Input v-model="editForm.measures" type="textarea" :rows="3" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <FormItem label="备注" prop="marker">
              <Input v-model="editForm.marker" type="textarea" :rows="3" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24" style="margin-left: 20px;">
            <template>
              <div class="demo-upload-list" v-for="item in editDefaultList">
                <template>
                  <img :src="item.url">
                  <div class="demo-upload-list-cover">
                    <Icon type="ios-eye-outline" @click.native="handleView(item.url)"></Icon>
                    <Icon type="ios-trash-outline" @click.native="handleRemove(item.url)"></Icon>
                  </div>
                </template>
              </div>
              <Modal title="查看照片" v-model="visible">
                <img :src="imgUrl" v-if="visible" style="width: 100%">
              </Modal>
            </template>
            <Upload
              ref="upload"
              name="img_pic"
              :default-file-list="editDefaultList"
              :on-success="editHandleSuccess"
              :format="['jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'pdf']"
              :max-size="600"
              :on-format-error="handleFormatError"
              :on-exceeded-size="handleMaxSize"
              :before-upload="handleBeforeUpload"
              multiple
              :data="upData"
              type="drag"
              action="/api/project/uploadPic"
              style="display: inline-block;width:58px;">
              <div style="width: 58px;height:58px;line-height: 58px;">
                <Icon type="ios-camera" size="20"></Icon>
              </div>
            </Upload>
            <div style="color:#ea856b">文件大小不能超过600KB,请确保上传完毕之后再提交保存</div>
            <!-- <div class="demo-upload-list" v-for="item in defaultList">
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
            </Modal> -->
          </Col>
        </Row>
      </Form>
      <div slot="footer">
        <Button
          @click="editSubmit('editForm')"
          :loading="submitLoading"
          type="primary"
          style="margin-left:8px"
        >保存
        </Button>
      </div>
    </Modal>
    <Modal
      :mask-closable="false"
      v-model="reasonModal"
      title="审核不通过原因">
      <Form ref="reasonForm" :model="reasonForm">
        <FormItem prop="reason">
          <Input type="textarea" size="large" v-model="reasonForm.reason" placeholder="请输入"/>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="primary" @click="reasonAudit()" :loading="reasonAuditLoading">确定</Button>
      </div>
    </Modal>
  </Card>
</template>
<script>
  import {
    getAuditedProjects,
    projectProgress,
    projectProgressList,
    projectPlanInfo,
    editProjectProgress,
    getData,
    auditProjectProgress,
    toAuditSchedule,
    actCompleteMoney,
    getProjectNoScheduleList,
    projectScheduleMonth,
    getProjectDictData,
    projectScheduleDelete
  } from '../../../api/project';
  import {initDepartment, loadDepartment, loadClassDepartment} from '../../../api/system';
  import './projectSchedule.css'

  export default {
    data() {
      return {
        pageSize: 10,   // 每页显示多少条
        dataCount: 0,   // 总条数
        pageCurrent: 1, // 当前页
        nowData: [],
        isShowButton: false,
        noScheduleButton: false,
        reasonModal: false,
        reasonAuditLoading: false,
        reasonForm: {
          reason: ''
        },
        openErrorAlert: false,
        showAuditButton: false,
        formId: '',
        dropDownContent: '展开',
        drop: false,
        dropDownIcon: "ios-arrow-down",
        btnDisable: true,
        upbtnDisabled: true,
        picDisable: true,
        dataDep1: [],
        dpLoading: false,
        searchForm: {
          title: '',
          project_num: '',
          subject: '',
          end_at: '',
          department_id: [],
          department_title: '',
          money_from: '',
          is_gc: '',
          nep_type: '',
          is_audit: ''
        },
        noSchedule: false,
        seeModal: false,
        editModal: false,
        scheduleColumns: [
          {
            title: '项目名称',
            key: 'title',
            width: 250,
            fixed: 'left'
          },
          {
            title: '项目编号',
            key: 'num',
            width: 210
          },
          {
            title: '负责人',
            key: 'username',
            width: 200
          },
          {
            title: '电话',
            key: 'phone',
            width: 150
          }
        ],
        columns: [
          {
            type: 'index2',
            width: 50,
            align: 'center',
            fixed: 'left',
            render: (h, params) => {
              return h('span', params.index + (this.pageCurrent - 1) * this.pageSize + 1);
            }
          },
          {
            title: '项目名称',
            key: 'project_title',
            width: 200,
            fixed: 'left'
          },
          {
            title: '填报月份',
            key: 'month',
            width: 100,
            fixed: 'left',
            align: "center"
          },
          {
            title: '项目编号',
            key: 'project_num',
            width: 100,
            align: "left"
          },
          {
            title: '投资主体',
            key: 'subject',
            width: 200
          },
          {
            title: '建设开始年限',
            key: 'build_start_at',
            width: 120,
            align: "center"
          },
          {
            title: '建设结束年限',
            key: 'build_end_at',
            width: 120,
            align: "center"
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
            width: 350
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
            width: 350
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
            width: 350
          },
          {
            title: '存在问题',
            key: 'problem',
            width: 250
          },
          {
            title: '整改措施',
            key: 'measures',
            width: 250
          },
          {
            title: '计划开工时间',
            key: 'plan_build_start_at',
            width: 120,
            align: "center"
          },
          {
            title: '填报员姓名',
            key: 'tianbao_name',
            width: 100
          },
          {
            title: '部门名称',
            key: 'department',
            width: 250
          },
          {
            title: '创建时间',
            key: 'created_at',
            width: 150,
            align: "right"
          },
          {
            title: '修改时间',
            key: 'updated_at',
            width: 150,
            align: "right"
          },
          {
            title: '审核状态',
            key: 'is_audit',
            fixed: 'right',
            width: 150,
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
            width: 180,
            fixed: 'right',
            align: 'center',
            render: (h, params) => {
              let editButton;
              editButton = this.office === 0 ? !(params.row.is_audit === 3 || params.row.is_audit === 2 || params.row.is_audit === 4) : this.office === 1;
              let delButton;
              delButton = this.office === 0 ? !(params.row.is_audit === 2 || params.row.is_audit === 4) : true;
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
                      this.month_act = params.row.month + ' 月实际完成投资(万元)';
                      this.year_investors = params.row.month.substring(0, 4) + '年计划投资(万元)';
                      this.year_img = params.row.month.substring(0, 4) + '年形象进度';
                      this.showAuditButton = this.office === 1 ? params.row.is_audit === 0 : false;
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
                          _seeThis.seeForm.project_title = em.project_title;
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
                          _seeThis.seeForm.measures = em.measures;
                          _seeThis.seeForm.plan_build_start_at = em.plan_build_start_at;
                          _seeThis.seeForm.exp_preforma = em.exp_preforma;
                          _seeThis.seeForm.is_audit = em.is_audit;
                          _seeThis.seeForm.reason = em.reason;
                          let img_pic = [];
                          if (em.img_progress_pic) {
                            let pics = em.img_progress_pic.split(",");
                            let i = 0;
                            pics.forEach(function (em_pic) {
                              if (em_pic != 'null') {
                                img_pic.push({url: em_pic.replace(/#/g, "%23"), name: i});
                              }
                              i++;
                            })
                          }

                          _seeThis.defaultList = img_pic;
                          _seeThis.seeForm.marker = em.marker;
                        }
                      });
                      this.openErrorAlert = (this.seeForm.reason !== '' && this.seeForm.is_audit === 2);
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
                      console.log(params);
                      this.month_img = params.row.month + ' 月形象进度';
                      this.month_act = params.row.month + ' 月实际完成投资(万元)';
                      this.year_investors = params.row.month.substring(0, 4) + '年计划投资(万元)';
                      this.year_img = params.row.month.substring(0, 4) + '年形象进度';
                      this.formId = params.row.id;
                      this.editForm.id = params.row.id;
                      let _editThis = this;
                      this.data.forEach(function (em) {
                        if (em.id === params.row.id) {
                          _editThis.editForm.month = em.month;
                          _editThis.editForm.project_id = em.project_id;
                          _editThis.editForm.project_num = em.project_num;
                          _editThis.editForm.project_title = em.project_title;
                          _editThis.editForm.subject = em.subject;
                          _editThis.editForm.build_start_at = em.build_start_at;
                          _editThis.editForm.build_end_at = em.build_end_at;
                          _editThis.editForm.total_investors = em.total_investors;
                          _editThis.editForm.plan_investors = em.plan_investors;
                          _editThis.editForm.plan_img_progress = em.plan_img_progress;
                          _editThis.editForm.month_img_progress = em.month_img_progress;
                          _editThis.editForm.month_act_complete = parseFloat(em.month_act_complete);
                          _editThis.editForm.acc_img_progress = em.acc_img_progress;
                          _editThis.editForm.acc_complete = em.acc_complete;
                          _editThis.editForm.problem = em.problem;
                          _editThis.editForm.measures = em.measures;
                          _editThis.editForm.is_audit = em.is_audit;
                          _editThis.editForm.reason = em.reason;
                          _editThis.editForm.plan_build_start_at = em.plan_build_start_at;
                          _editThis.editForm.exp_preforma = em.exp_preforma;
                          _editThis.editForm.img_progress_pic = em.img_progress_pic;

                          _editThis.upData = {month: em.month, project_num: em.project_num, project_id: em.project_id};

                          let edit_img_pic = [];
                          if (em.img_progress_pic) {
                            let edit_pics = em.img_progress_pic.split(",");
                            edit_pics.forEach(function (item) {
                              let files = item.split("/");
                              let fileName = files[files.length - 1];
                              if (fileName !== 'null') {
                                edit_img_pic.push({url: item.replace(/#/g, "%23"), name: fileName});
                              }
                            })
                          }
                          _editThis.editDefaultList = edit_img_pic;
                          _editThis.editForm.marker = em.marker;
                        }
                      });
                      this.openErrorAlert = (this.editForm.reason !== '' && this.editForm.is_audit === 2);
                      this.editModal = true;
                      $('.ivu-upload-list').remove();
                    }
                  }
                }, '编辑'),
                h('Button', {
                  props: {
                    type: 'error',
                    size: 'small',
                    disabled: delButton,
                    // loading: _this.editFormLoading
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      this.$Modal.confirm({
                        title: "确认删除",
                        loading: true,
                        content: "您确认要删除这个项目进度？",
                        onOk: () => {
                          projectScheduleDelete({id: params.row.id}).then(res => {
                            if (res.result === true) {
                              this.$Message.success("删除成功");
                              this.init();
                            } else {
                              this.$Message.error("项目进度不能删除");
                            }
                            this.$Modal.remove();
                          });
                        }
                      });
                    }
                  }
                }, '删除')
              ]);
            }
          }
        ],
        data: [],
        scheduleData: [],
        tableLoading: true,
        loading: false,
        month_img: '-月形象进度',
        month_act: '-月实际完成投资(万元)',
        year_investors: '--年计划投资(万元)',
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
          month_act_complete: null,
          month_img_progress: '',
          acc_complete: '',
          acc_img_progress: '',
          problem: '',
          measures: '',
          plan_build_start_at: '',
          exp_preforma: '',
          img_progress_pic: '',
          marker: '',
          is_audit: ''
        },
        editForm: {
          month: '',
          project_id: '',
          project_num: '',
          subject: '',
          build_start_at: '',
          build_end_at: '',
          total_investors: '',
          plan_investors: '',
          plan_img_progress: '',
          month_act_complete: null,
          month_img_progress: '',
          acc_complete: '',
          acc_img_progress: '',
          problem: '',
          measures: '',
          plan_build_start_at: '',
          exp_preforma: '',
          img_progress_pic: '',
          marker: '',
          is_audit: ''
        },
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
            {required: true, message: '实际完成投资不能为空', trigger: 'blur', type: 'number'}
          ],
          exp_preforma: [
            {required: true, message: '土地征收情况及前期手续办理情况不能为空', trigger: 'blur'}
          ],
          month_img_progress: [
            {required: true, message: '月形象进度不能为空', trigger: 'blur'}
          ],
          acc_img_progress: [
            {required: true, message: '累计形象进度不能为空', trigger: 'blur'}
          ],
          problem: [
            {required: true, message: '存在问题不能为空', trigger: 'blur'}
          ],
          measures: [
            {required: true, message: '整改措施不能为空', trigger: 'blur'}
          ]
        },
        project_id: [],
        imgName: '',
        visible: false,
        uploadList: [],
        imgUrl: '',
        defaultList: [],
        editDefaultList: [],
        is_audit: [],
        editButton: false,
        upData: {},
        month_options_0: {
          disabledDate(date) {
            let date_at = new Date();
            let curr_time_0 = (date_at.getMonth() + 1) > 9 ? (date_at.getMonth() + 1) : '0' + (date_at.getMonth() + 1);
            let curr_time = date_at.getFullYear() + '-' + curr_time_0;

            let time_0 = (date.getMonth() + 1) > 9 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            let time = date.getFullYear() + '-' + time_0;
            const disabledMonth = time;
            return disabledMonth !== curr_time;
            // return disabledMonth > curr_time;
          }
        },
        scheduleMonth: [],
        searchNepDisabled: true,
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
        visible: false,
      }
    },
    methods: {
      init() {
        this.getProjectScheduleList();
        this.month_im = '-月形象进度';
        this.month_ac = '-月实际完成投资(万元)';
        this.year_investor = '--年计划投资(万元)';
        this.year_im = '--年形象进度';

        this.office = this.$store.getters.user.office;
        this.isShowButton = this.office === 0;
        this.noScheduleButton = this.office === 1;
        loadClassDepartment().then(res => {
          this.dataDep1 = res;
        });
        // initDepartment().then(res => {
        //   res.result.forEach(function (e) {
        //     if (e.is_parent) {
        //       e.loading = false;
        //       e.children = [];
        //     }
        //     if (e.status === 0) {
        //       e.title = "[已禁用] " + e.title;
        //       e.disabled = true;
        //     }
        //   });
        //   this.dataDep = res.result;
        //   console.log(this.dataDep);
        // this.loadDataTree();
        this.getDictData();
        // });
        this.$refs.form.resetFields();// 获取项目名称
        this.getProjectId();
        this.getProjectNoScheduleList();
      },
      getProjectScheduleList() {
        this.tableLoading = true;
        this.searchForm.is_audit = this.$route.params.is_audit;
        projectProgressList(this.searchForm).then(res => {
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
          if (this.searchForm.is_gc>=0 || this.searchForm.nep_type>=0 || this.searchForm.money_from>=0 || this.searchForm.department_id>=0 || this.searchForm.title>=0 || this.searchForm.project_id>=0|| this.searchForm.project_num>=0 || this.searchForm.subject>=0 || this.searchForm.end_at>=0 || this.searchForm.title>=0) {
            this.btnDisable = false;
          }
          this.tableLoading = false;
        });
      },
      getProjectNoScheduleList() {
        getProjectNoScheduleList().then(res => {  
          this.scheduleData = res.result;
        });
      },
      audit(name) {
        if (parseInt(name) === 1) {
          let params = {id: this.formId, status: parseInt(name), reason: ''};
          this.toAuditProject(params)
        } else {
          this.reasonModal = true;
          this.reasonForm.id = this.formId;
          this.reasonForm.status = parseInt(name);
        }
      },
      reasonAudit() {
        this.reasonAuditLoading = true;
        this.toAuditProject(this.reasonForm);
      },
      toAuditProject(params) {
        auditProjectProgress(params).then(res => {
          if (res.result === true) {
            if (params.status === 1) {
              this.$Message.success('审核通过!');
            } else {
              this.reasonAuditLoading = false;
              this.reasonModal = false;
              this.$Message.error('审核不通过!');
            }
            this.seeModal = false;
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
        getAuditedProjects().then(res => {
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
            this.form.plan_build_start_at = em.plan_start_at;
            
            let month_time = new Date();
            let month_time_0 = (month_time.getMonth() + 1) > 9 ? (month_time.getMonth() + 1) : '0' + (month_time.getMonth() + 1);

            month_time = month_time.getFullYear() + '-' + month_time_0;
            
            this.month_img = month_time + ' 月形象进度';
            this.month_act = month_time + ' 月实际完成投资(万元)';
            this.year_investors = month_time.substring(0, 4) + '年计划投资(万元)';
            this.year_img = month_time.substring(0, 4) + '年形象进度';
            projectPlanInfo({month: month_time, project_id: this.form.project_id}).then(res => {
              this.form.plan_investors = res.result.amount;
              this.form.plan_img_progress = res.result.image_progress;
            });
          }
        });
      },
      changeMonth(e) {
        if (this.form.project_id === '') {
          this.$Message.error('请先选择填报项目!');
          this.form.month = '';
          return;
        }
        this.upbtnDisabled = false;
        let month_time = new Date(this.form.month);
        let month_time_0 = (month_time.getMonth() + 1) > 9 ? (month_time.getMonth() + 1) : '0' + (month_time.getMonth() + 1);

        month_time = month_time.getFullYear() + '-' + month_time_0;
        this.upData = {month: month_time, project_num: this.form.project_num, project_id: this.form.project_id};
        // this.month_img = e + ' 月形象进度';
        // this.month_act = e + ' 月实际完成投资(万元)';
        // this.year_investors = e.substring(0, 4) + '年计划投资(万元)';
        // this.year_img = e.substring(0, 4) + '年形象进度';
        // if (this.form.project_id) {
        //   projectPlanInfo({month: this.form.month, project_id: this.form.project_id}).then(res => {
        //     this.form.plan_investors = res.result.amount;
        //     this.form.plan_img_progress = res.result.image_progress;
        //   });
        // }
      },// 月实际完成投资发生改变时 改变累计投资
      changeMonthActComplete(e) {
        if (this.form.project_id === '') {
          this.$Message.error('请先选择填报项目!');
          this.form.month_act_complete = null;
          return;
        }
        if (this.form.month === '') {
          this.$Message.error('请先选择填报时间!');
          this.form.month_act_complete = null;
          return;
        }
        actCompleteMoney({
          month: this.form.month,
          project_id: this.form.project_id,
          month_act_complete: this.form.month_act_complete,
          type: 'add'
        }).then(res => {
          this.form.acc_complete = res.result;
        });
      },
      changeEditMonthActComplete(e) {
        actCompleteMoney({
          month: this.editForm.month,
          project_id: this.editForm.project_id,
          month_act_complete: this.editForm.month_act_complete,
          type: 'edit'
        }).then(res => {
          this.editForm.acc_complete = res.result;

        });
      },
      handleReset(name) {
        this.$refs[name].resetFields();
        this.$refs.upload.clearFiles();
      },
      handleClearFiles() {
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
        this.month_ac = '-月实际完成投资(万元)';
        this.year_investor = '--年计划投资(万元)';
        this.year_im = '--年形象进度';
      },//上传图片
      handleView(url) {
        this.imgUrl = url;
        this.visible = true;
      },
      handleRemove(file) {
        const fileList = this.editDefaultList;
        fileList.forEach(function (fileObj, index) {
          if (file === fileObj.url) {
            fileList.splice(index, 1);
          }
        });
        if (fileList.length > 0) {
          let fileUrl = [];
          fileList.forEach(function (obj) {
            fileUrl.push(obj.url);
          });
          this.editForm.img_progress_pic = fileUrl.join(',');
        } else {
          this.editForm.img_progress_pic = '';
        }
      },
      handleSuccess(res, file) {
        if (this.form.img_progress_pic) {
          this.form.img_progress_pic = this.form.img_progress_pic + ',' + res.result;
        } else {
          this.form.img_progress_pic = res.result;
        }
      },
      editHandleSuccess(res, file) {
        let files = res.result.split("/");
        let fileName = files[files.length - 1];
        if (fileName !== 'null') {
          this.editDefaultList.push({url: res.result, name: fileName});
        }
        if (this.editForm.img_progress_pic) {
          console.log(this.editForm.img_progress_pic);

          this.editForm.img_progress_pic = this.editForm.img_progress_pic + ',' + res.result;
        } else {
          this.editForm.img_progress_pic = res.result;
        }
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
          desc: '文件太大，不能超过600KB'
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
      exportSchedule() {
        this.picDisable = false;
        let title = this.searchForm.title;
        let project_id = this.searchForm.project_id;
        let project_num = this.searchForm.project_num;
        let subject = this.searchForm.subject;
        let end_at = this.searchForm.end_at;
        let department_id = this.searchForm.department_id;
        let is_gc = this.searchForm.is_gc;
        let nep_type = this.searchForm.nep_type;
        let money_from = this.searchForm.money_from;
        let start_time = '';
        let end_time = '';
        if (end_at) {
          let end_time_0 = new Date(end_at);
          let month_end_time_0 = (end_time_0.getMonth() + 1) > 9 ? (end_time_0.getMonth() + 1) : '0' + (end_time_0.getMonth() + 1);
          end_time = end_time_0.getFullYear() + '-' + month_end_time_0;
        }
        window.location.href = "/api/project/exportSchedule?title=" + title + "&project_id=" + project_id + "&project_num=" + project_num + "&subject=" + subject + "&end_at=" + end_time + "&department_id=" + department_id+ "&is_gc=" + is_gc+ "&nep_type=" + nep_type+ "&money_from=" + money_from;
      },//下载
      downloadPic() {
        let title = this.searchForm.title;
        let project_id = this.searchForm.project_id;
        let project_num = this.searchForm.project_num;
        let subject = this.searchForm.subject;
        let end_at = this.searchForm.end_at;
        let department_id = this.searchForm.department_id;
        let is_gc = this.searchForm.is_gc;
        let nep_type = this.searchForm.nep_type;
        let money_from = this.searchForm.money_from;
        let start_time = '';
        let end_time = '';
        if (end_at) {
          let end_time_0 = new Date(end_at);
          let month_end_time_0 = (end_time_0.getMonth() + 1) > 9 ? (end_time_0.getMonth() + 1) : '0' + (end_time_0.getMonth() + 1);
          end_time = end_time_0.getFullYear() + '-' + month_end_time_0;
        }
        window.location.href = "/api/project/downLoadSchedule?title=" + title + "&project_id=" + project_id + "&project_num=" + project_num + "&subject=" + subject + "&end_at=" + end_time + "&department_id=" + department_id+ "&is_gc=" + is_gc+ "&nep_type=" + nep_type+ "&money_from=" + money_from;
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
      loadDataTree(item, callback) {
        loadDepartment(item.id).then(res => {
          res.result.forEach(function (e) {
            if (e.is_parent) {
              e.loading = false;
              e.children = [];
            }
            if (e.status === 0) {
              e.title = "[已禁用] " + e.title;
              e.disabled = true;
            }
          });
          callback(res.result);
        });
      },
      selectTreeS(v) {
        if (v.length > 0) {
          // 转换null为""
          for (let attr in v[0]) {
            if (v[0][attr] === null) {
              v[0][attr] = "";
            }
          }
          let str = JSON.stringify(v[0]);
          let data = JSON.parse(str);
          this.searchForm.department_id = data.id;
          this.searchForm.department_title = data.title;
        }
      },
      onSearchIsGcChange(value) {
        this.searchNepDisabled = value !== 1;
        if (this.searchNepDisabled) {
          this.searchForm.nep_type = '';
        }
      },
      getDictData() {
        getProjectDictData(this.dictName).then(res => {
          if (res.result) {
            this.dict = res.result;
          }
        });
      },
      format(labels, selectedData) {
        const index = labels.length - 1;
        const data = selectedData[index] || false;
        if (data && data.code) {
          return labels[index];
        }
        return labels[index];
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
