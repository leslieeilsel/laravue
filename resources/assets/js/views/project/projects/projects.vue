<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="70" class="search-form">
        <Form-item label="项目名称" prop="title">
          <Select v-model="searchForm.title" filterable style="width: 200px">
            <Option v-for="item in data" :value="item.title" :key="item.id">{{ item.title }}</Option>
          </Select>
        </Form-item>
        <Form-item label="项目编号" prop="num">
          <Input
            type="text"
            v-model="searchForm.num"
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
          <Form-item label="承建单位" prop="unit">
            <Select v-model="searchForm.unit" filterable style="width: 200px">
              <Option v-for="item in data" :value="item.unit" :key="item.id">{{ item.unit }}</Option>
            </Select>
          </Form-item>
          <FormItem label="项目类型" prop="type">
            <Select v-model="searchForm.type" style="width: 200px">
              <Option v-for="item in dict.type" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="建设性质" prop="build_type">
            <Select v-model="searchForm.build_type" style="width: 200px" filterable>
              <Option v-for="item in dict.build_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="资金来源">
            <Select v-model="searchForm.money_from" prop="money_from" style="width: 200px">
              <Option v-for="item in dict.money_from" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="项目标识" prop="is_gc">
            <Select @on-change="onSearchIsGcChange" v-model="searchForm.is_gc" style="width: 200px"
                    placeholder="是否为国民经济计划">
              <Option v-for="item in dict.is_gc" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
          <FormItem label="国民经济计划分类" prop="nep_type">
            <Select v-model="searchForm.nep_type" style="width: 200px" :disabled="searchNepDisabled">
              <Option v-for="item in dict.nep_type" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
          <FormItem label="项目状态" prop="status">
            <Select v-model="searchForm.status" style="width: 200px">
              <Option v-for="item in dict.status" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
        </span>
        <Form-item style="margin-left:-70px;" class="br">
          <Button @click="getProject" type="primary" icon="ios-search">搜索</Button>
          <Button @click="handleResetSearch">重置</Button>
          <a class="drop-down" @click="dropDown">
            {{dropDownContent}}
            <Icon :type="dropDownIcon"></Icon>
          </a>
        </Form-item>
      </Form>
    </Row>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">添加项目</Button>
      <Button type="error" disabled icon="md-trash">删除</Button>
    </p>
    <Row>
      <Table type="selection" stripe border :columns="columns" :data="data" :loading="tableLoading"></Table>
    </Row>
    <Modal
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="创建项目">
      <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="110">
        <Divider><h4>基本信息</h4></Divider>
        <Row>
          <Col span="12">
            <FormItem label="项目名称" prop="title">
              <Input v-model="form.title" placeholder="必填项"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目编号" prop="num">
              <Input v-model="form.num" placeholder="必填项"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目类型" prop="type">
              <Select v-model="form.type">
                <Option v-for="item in dict.type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="form.subject" placeholder="必填项"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="建设性质" prop="build_type">
              <Select v-model="form.build_type">
                <Option v-for="item in dict.build_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="承建单位" prop="unit">
              <Input v-model="form.unit" placeholder="必填项"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="资金来源" prop="money_from">
              <Select v-model="form.money_from">
                <Option v-for="item in dict.money_from" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目金额(万元)" prop="amount">
              <Input v-model="form.amount" placeholder="必填项"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="土地费用(万元)" prop="land_amount">
              <Input v-model="form.land_amount" placeholder="必填项"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目状态" prop="status">
              <Select v-model="form.status">
                <Option v-for="item in dict.status" :value="item.value" :key="item.value">{{item.title}}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目标识(是否为国民经济计划)" prop="is_gc">
              <Select v-model="form.is_gc" @on-change="onAddIsGcChange">
                <Option v-for="item in dict.is_gc" :value="item.value" :key="item.value">{{item.title}}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="国民经济计划分类" prop="nep_type">
              <Select v-model="form.nep_type" :disabled="addNepDisabled">
                <Option v-for="item in dict.nep_type" :value="item.value" :key="item.value">{{item.title}}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="计划开始时间" prop="plan_start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy年MM月" v-model="form.plan_start_at"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="计划结束时间" prop="plan_end_at">
              <DatePicker type="month" @on-change="buildYearPlan" placeholder="结束时间" format="yyyy年MM月"
                          v-model="form.plan_end_at"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
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
          </Col>
          <Col span="12">
            <FormItem label="项目概况" prop="description">
              <Input v-model="form.description" type="textarea" :rows="4" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Divider><h4>项目投资计划</h4></Divider>
        <div v-for="(item, index) in form.projectPlan">
          <Divider orientation="left"><h5 style="color: #2d8cf0;">{{item.date}}年项目投资计划</h5></Divider>
          <Row>
            <Col span="12">
              <FormItem
                label="计划投资金额"
                :prop="'projectPlan.' + index + '.amount'"
                :rules="{required: true, message: '计划投资金额不能为空', trigger: 'blur'}">
                <Input v-model="item.amount" placeholder="单位万元"/>
              </FormItem>
            </Col>
            <Col span="12">
              <FormItem
                label="计划形象进度"
                :rules="{required: true, message: '计划形象进度不能为空', trigger: 'blur'}"
                :prop="'projectPlan.' + index + '.image_progress'">
                <Input v-model="item.image_progress" type="textarea" :rows="1" placeholder="请输入..."></Input>
              </FormItem>
            </Col>
          </Row>
          <Row>
            <Collapse>
              <Panel name="index">
                填写月项目投资计划（非必填）
                <Row slot="content">
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
                      <Input type="text" placeholder="" v-model="ite.date + '月'" readonly class="monthInput"/>
                    </Col>
                    <Col span="8">
                      <Input
                        :prop="'month.' + item.date + ite.date + '.amount'"
                        :rules="{required: true, message: '月计划投资金额不能为空', trigger: 'blur'}"
                        type="text" placeholder="必填项" v-model="ite.amount" class="monthInput"/>
                    </Col>
                    <Col span="8">
                      <Input type="text" placeholder="请输入..." v-model="ite.image_progress" class="monthInput"/>
                    </Col>
                  </div>
                </Row>
              </Panel>
            </Collapse>
          </Row>
        </div>
      </Form>
      <div slot="footer">
        <Button @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
        <Button type="primary" @click="handleSubmit('formValidate')" :loading="loading">保存</Button>
      </div>
    </Modal>
    <Modal
      v-model="editModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="修改项目">
      <Form ref="editFormValidate" :model="editForm" :rules="ruleValidate" :label-width="110">
        <Divider><h4>基本信息</h4></Divider>
        <Row>
          <Col span="12">
            <FormItem label="项目名称" prop="title">
              <Input v-model="editForm.title" placeholder="必填项" v-bind:readonly="isReadOnly"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目编号" prop="num">
              <Input v-model="editForm.num" placeholder="必填项" v-bind:readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目类型" prop="type">
              <Select v-model="editForm.type">
                <Option v-for="item in dict.type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="editForm.subject" placeholder="必填项" v-bind:readonly="isReadOnly"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="建设性质" prop="build_type">
              <Select v-model="editForm.build_type">
                <Option v-for="item in dict.build_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="承建单位" prop="unit">
              <Input v-model="editForm.unit" placeholder="必填项" v-bind:readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="资金来源" prop="money_from">
              <Select v-model="editForm.money_from">
                <Option v-for="item in dict.money_from" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目金额(万元)" prop="amount">
              <Input v-model="editForm.amount" placeholder="单位万元" v-bind:readonly="isReadOnly"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="土地费用(万元)" prop="land_amount">
              <Input v-model="editForm.land_amount" placeholder="单位万元" v-bind:readonly="isReadOnly"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目状态" prop="status">
              <Select v-model="editForm.status">
                <Option v-for="item in dict.status" :value="item.value" :key="item.value">{{item.title}}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目标识(是否为国民经济计划)" prop="is_gc">
              <Select v-model="editForm.is_gc" @on-change="onAddIsGcChange">
                <Option v-for="item in dict.is_gc" :value="item.value" :key="item.value">{{item.title}}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="国民经济计划分类" prop="nep_type">
              <Select v-model="editForm.nep_type" :disabled="addNepDisabled">
                <Option v-for="item in dict.nep_type" :value="item.value" :key="item.value">{{item.title}}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="计划开始时间" prop="plan_start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy年MM月"
                          v-model="editForm.plan_start_at"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="计划结束时间" prop="plan_end_at">
              <DatePicker type="month" @on-change="buildYearPlan" placeholder="结束时间" format="yyyy年MM月"
                          v-model="editForm.plan_end_at"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <FormItem label="项目中心点坐标" prop="center_point">
            <Input v-model="editForm.center_point" placeholder="必填项" v-bind:readonly="isReadOnly"/>
          </FormItem>
          <FormItem label="项目轮廓点坐标" prop="positions">
            <Input v-model="editForm.positions" placeholder="必填项" readonly/>
          </FormItem>
        </Row>
        <Row>
          <FormItem label="项目概况" prop="description">
            <Input v-model="editForm.description" type="textarea" :rows="4" placeholder="请输入..."
                   v-bind:readonly="isReadOnly"></Input>
          </FormItem>
        </Row>
        <Divider><h4>投资计划</h4></Divider>
        <div v-for="(item, index) in editForm.projectPlan">
          <Divider orientation="left"><h5 style="color: #2d8cf0;">{{item.date}}年项目投资计划</h5></Divider>
          <Row>
            <Col span="12">
              <FormItem
                label="计划投资金额"
                :prop="'projectPlan.' + index + '.amount'"
                :rules="{required: true, message: '计划投资金额不能为空', trigger: 'blur'}">
                <Input v-model="item.amount" placeholder="单位万元" v-bind:readonly="isReadOnly"/>
              </FormItem>
            </Col>
            <Col span="12">
              <FormItem
                label="计划形象进度"
                :rules="{required: true, message: '计划形象进度不能为空', trigger: 'blur'}"
                :prop="'projectPlan.' + index + '.image_progress'">
                <Input v-model="item.image_progress" type="textarea" :rows="1" placeholder="请输入..."
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
                <Input type="text" placeholder="" v-model="ite.date + '月'" readonly class="monthInput"/>
              </Col>
              <Col span="8">
                <Input
                  :prop="'month.' + item.date + ite.date + '.amount'"
                  :rules="{required: true, message: '月计划投资金额不能为空', trigger: 'blur'}"
                  type="text" placeholder="必填项" v-model="ite.amount" class="monthInput"/>
              </Col>
              <Col span="8">
                <Input type="text" placeholder="请输入..." v-model="ite.image_progress" class="monthInput"/>
              </Col>
            </div>
          </Row>
        </div>
      </Form>
      <div slot="footer">
        <Button type="primary" @click="editSubmit('editFormValidate')" :loading="loading">保存</Button>
      </div>
    </Modal>
    <Modal
      v-model="previewModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="查看项目">
      <Form ref="previewFormValidate" :model="previewForm" :label-width="110">
        <Divider><h4>基本信息</h4></Divider>
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
              <DatePicker type="month" v-bind:readonly="isReadOnly" @on-change="buildYearPlan" placeholder=""
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
                label="计划投资金额"
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
                <Input
                  :prop="'month.' + item.date + ite.date + '.amount'"
                  :rules="{required: true, message: '月计划投资金额不能为空', trigger: 'blur'}"
                  type="text" placeholder="" v-model="ite.amount" readonly class="monthInput"/>
              </Col>
              <Col span="8">
                <Input type="text" placeholder="" v-model="ite.image_progress" readonly class="monthInput"/>
              </Col>
            </div>
          </Row>
        </div>
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
  </Card>
</template>
<script>
  import {
    edit,
    getEditFormData,
    getAllProjects,
    addProject,
    getProjectDictData,
    buildPlanFields,
    auditProject
  } from '../../../api/project';
  import './projects.css'

  export default {
    data() {
      return {
        dropDownContent: '展开',
        drop: false,
        dropDownIcon: "ios-arrow-down",
        isReadOnly: false,
        btnDisable: true,
        editFormLoading: false,
        searchForm: {
          title: '',
          subject: '',
          unit: '',
          num: '',
          type: '',
          build_type: '',
          money_from: '',
          is_gc: '',
          nep_type: '',
          status: '',
        },
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
            width: 100,
            align: "center"
          },
          {
            title: '投资主体',
            key: 'subject',
            width: 210
          },
          {
            title: '项目类型',
            key: 'type',
            width: 100,
            align: "center"
          },
          {
            title: '承建单位',
            key: 'unit',
            width: 210
          },
          {
            title: '建设性质',
            key: 'build_type',
            width: 90,
            align: "center"
          },
          {
            title: '资金来源',
            key: 'money_from',
            width: 90,
            align: "center"
          },
          {
            title: '项目金额(万元)',
            key: 'amount',
            width: 120,
            align: "right"
          },
          {
            title: '土地费用(万元)',
            key: 'land_amount',
            width: 120,
            align: "right"
          },
          {
            title: '是否为国民经济计划',
            key: 'is_gc',
            width: 150,
            align: "center"
          },
          {
            title: '国民经济计划分类',
            key: 'nep_type',
            width: 150,
            align: "center"
          },
          // {
          //   title: '计划开始时间',
          //   key: 'plan_start_at',
          //   width: 120
          // },
          // {
          //   title: '计划结束时间',
          //   key: 'plan_end_at',
          //   width: 120
          // },
          {
            title: '审核状态',
            key: 'is_audit',
            fixed: 'right',
            width: 160,
            render: (h, params) => {
              const row = params.row;
              const color = row.is_audit === 0 ? 'warning' : row.is_audit === 1 ? 'success' : row.is_audit === 2 ? 'error' : 'primary';
              const text = row.is_audit === 0 ? '待审核' : row.is_audit === 1 ? '审核通过' : row.is_audit === 2 ? '审核不通过' : '投资调整';

              return h('Tag', {
                props: {
                  type: 'dot',
                  color: color
                }
              }, text);
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
              let _this = this;
              const groupId = this.$store.getters.user.group_id;
              if (groupId === 6) {
                this.showAuditButton = false;
                if (params.row.is_audit === 3) {
                  editButton = false;
                } else {
                  const row = params.row;
                  editButton = row.is_edit === 0 ? true : false;
                }
              }
              if (groupId === 4 || groupId === 7) {
                this.showAuditButton = true;
                editButton = false;
              }
              return h('div', [
                h('Button', {
                  props: {
                    type: 'primary',
                    size: 'small'
                  },
                  style: {
                    marginRight: '5px',
                  },
                  on: {
                    click: () => {
                      this.previewForm = params.row;
                      this.formId = params.row.id;
                      this.isReadOnly = true;
                      this.previewModal = true;
                    }
                  }
                }, '查看'),
                h('Button', {
                  props: {
                    type: 'primary',
                    size: 'small',
                    disabled: editButton,
                    // loading: _this.editFormLoading
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      // this.editFormLoading = true;
                      getEditFormData(params.row.id).then(res => {
                        this.editForm = res.result;
                        this.addNepDisabled = this.editForm.is_gc !== 1;
                        if (this.addNepDisabled) {
                          this.editForm.nep_type = '';
                        }
                        this.formId = params.row.id;
                        this.isReadOnly = false;
                        this.editModal = true;
                        // this.editFormLoading = false;
                      });
                    }
                  }
                }, '编辑')
              ]);
            }
          }
        ],
        data: [],
        addNepDisabled: true,
        searchNepDisabled: true,
        tableLoading: true,
        loading: false,
        dictName: {
          type: '工程类项目分类',
          is_gc: '是否为国民经济计划',
          nep_type: '国民经济计划分类',
          status: '项目状态',
          money_from: '资金来源',
          build_type: '建设性质'
        },
        dict: {
          type: [],
          is_gc: [],
          nep_type: [],
          status: [],
          money_from: [],
          build_type: []
        },
        formId: '',
        form: {
          title: '',
          num: '',
          subject: '',
          type: '',
          build_type: '',
          money_from: '',
          status: '',
          unit: '',
          amount: '',
          land_amount: '',
          is_gc: '',
          nep_type: '',
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
              image_progress: '',
              month: [
                {
                  date: 1,
                  amount: '',
                  image_progress: ''
                }
              ]
            },
          ],
        },
        editForm: {},
        previewForm: {},
        index: 1,
        modal: false,
        previewModal: false,
        showAuditButton: true,
        editModal: false,
        ruleValidate: {
          title: [
            {required: true, message: '项目名称不能为空', trigger: 'blur'}
          ],
          num: [
            {required: true, message: '项目编号不能为空', trigger: 'blur'}
          ],
          status: [
            {required: true, message: '建设状态不能为空', trigger: 'change', type: 'number'}
          ],
          build_type: [
            {required: true, message: '建设性质不能为空', trigger: 'change', type: 'number'}
          ],
          money_from: [
            {required: true, message: '资金来源不能为空', trigger: 'change', type: 'number'}
          ],
          subject: [
            {required: true, message: '投资主体不能为空', trigger: 'blur'}
          ],
          type: [
            {required: true, message: '项目类型不能为空', trigger: 'change', type: 'number'}
          ],
          amount: [
            {required: true, message: '项目金额不能为空', trigger: 'blur'}
          ],
          is_gc: [
            {required: true, message: '项目标识不能为空', trigger: 'change', type: 'number'}
          ],
          unit: [
            {required: true, message: '建设单位不能为空', trigger: 'blur'}
          ],
          center_point: [
            {required: true, message: '项目中心点坐标不能为空', trigger: 'blur'}
          ],
          plan_start_at: [
            {required: true, message: '预送达时间不能为空', trigger: 'change', type: 'date'},
          ],
          plan_end_at: [
            {required: true, message: '预送达时间不能为空', trigger: 'change', type: 'date'},
          ],
        },
      }
    },
    methods: {
      init() {
        const groupId = this.$store.getters.user.group_id;
        if (groupId === 6) {
          this.showAuditButton = false;
          this.editButton = false;
        }
        if (groupId === 4 || groupId === 7) {
          this.showAuditButton = true;
          this.editButton = true;
        }
        this.$refs.formValidate.resetFields();
        this.getDictData();
        this.getProject();
      },
      getProject() {
        this.tableLoading = true;
        getAllProjects(this.searchForm).then(e => {
          this.data = e.result;
          this.tableLoading = false;
        });
      },
      getDictData() {
        getProjectDictData(this.dictName).then(res => {
          if (res.result) {
            this.dict = res.result;
          }
        });
      },
      handleResetSearch() {
        this.searchForm = {
          num: '',
          type: '',
          build_type: '',
          money_from: '',
          is_gc: '',
          nep_type: '',
          status: '',
        };
        this.getProject();
      },
      handleSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            addProject(this.form).then(e => {
              this.loading = false;
              if (e.result) {
                this.$Message.success('添加成功!');
                this.modal = false;
                this.init();
              } else {
                this.$Message.error('添加失败!');
              }
            });
          } else {
            this.$Message.error('请填写必填字段!');
          }
        })
      },
      editSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            edit(this.editForm).then(e => {
              this.loading = false;
              if (e.result) {
                this.$Message.success('修改成功!');
                this.editModal = false;
                this.init();
              } else {
                this.$Message.error('修改失败!');
              }
            });
          } else {
            this.$Message.error('请填写必填字段!');
          }
        })
      },
      handleReset(name) {
        this.$refs[name].resetFields();
      },
      handleAdd() {
        this.index++;
        this.form.positions.push({
          value: '',
          index: this.index,
          status: 1
        });
      },
      handleRemove(index) {
        this.form.positions[index].status = 0;
      },
      cancel() {
        this.$refs.formValidate.resetFields();
        this.form.group_id = this.checkedDefaultRole;
      },
      buildYearPlan() {
        let startDate = this.form.plan_start_at;
        let endDate = this.form.plan_end_at;
        buildPlanFields([startDate, endDate]).then(res => {
          if (res.result) {
            this.form.projectPlan = res.result;
          }
        });
      },
      audit(name) {
        auditProject({id: this.formId, status: name}).then(res => {
          if (res.result === true) {
            this.previewModal = false;
            this.$Message.success('审核状态修改成功!');
            this.getProject();
          }
        });
      },
      onAddIsGcChange(value) {
        this.addNepDisabled = value !== 1;
        if (this.addNepDisabled) {
          this.form.nep_type = '';
        }
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
      },
    },
    mounted() {
      this.init();
    }
  }
</script>
