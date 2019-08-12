<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="70" class="search-form">
        <Form-item label="项目名称" prop="title">
          <Input clearable v-model="searchForm.title" placeholder="支持模糊搜索" style="width: 200px"/>
        </Form-item>
        <Form-item label="项目编号" prop="num">
          <Input clearable v-model="searchForm.num" placeholder="支持模糊搜索" style="width: 200px"/>
        </Form-item>
        <span v-if="drop">
          <Form-item label="投资主体" prop="subject">
            <Input clearable v-model="searchForm.subject" placeholder="支持模糊搜索" style="width: 200px"/>
          </Form-item>
          <FormItem label="承建单位" prop="department_id">
            <Cascader :data="dataDep1" v-model="searchForm.department_id" placeholder="选择部门" trigger="hover"
                      style="width: 200px" :render-format="format"></Cascader>
          </FormItem>
          <FormItem label="项目类型" prop="type">
            <Select v-model="searchForm.type" style="width: 200px">
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.type" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
          </FormItem>
          <FormItem label="建设性质" prop="build_type">
            <Select v-model="searchForm.build_type" style="width: 200px" filterable>
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.build_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
            </Select>
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
          <FormItem label="项目状态" prop="status">
            <Select v-model="searchForm.status" style="width: 200px">
              <Option value='-1' key='-1'>全部</Option>
              <Option v-for="item in dict.status" :value="item.value" :key="item.value">{{item.title}}</Option>
            </Select>
          </FormItem>
        </span>
        <Form-item style="margin-left:-70px;" class="br">
          <Button @click="searchProject" type="primary" icon="ios-search">搜索</Button>
          <Button @click="handleResetSearch">重置</Button>
          <a class="drop-down" @click="dropDown">
            {{dropDownContent}}
            <Icon :type="dropDownIcon"></Icon>
          </a>
        </Form-item>
      </Form>
    </Row>
    <p class="btnGroup">
      <Button type="primary" @click="addProject" icon="md-add" v-if="isShowButton">添加项目</Button>
      <Button class="exportReport" @click="exportSchedule" v-if="showExportButton" type="primary"
              :disabled="exportBtnDisable" icon="md-cloud-upload">
        导出项目
      </Button>
      <Button v-if="isShowAdjustmentBtn" type="primary" @click="projectAdjustment" icon=""
              :disabled="projectBtnDisable">发起项目调整
      </Button>
    </p>
    <Row>
      <Table type="selection" stripe border :columns="columns" :data="data" :loading="tableLoading"
             @on-selection-change="checkboxProject"></Table>
    </Row>
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
      :mask-closable="false"
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="创建项目">
      <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="143">
        <Divider><h4>基础信息</h4></Divider>
        <Row>
          <Col span="12">
            <FormItem label="项目名称" prop="title">
              <Input v-model="form.title" placeholder="必填项"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目编号" prop="num">
              <Input v-model="form.num" placeholder="由发展经营部统一填写"></Input>
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
              <Input v-model="form.unit" readonly placeholder="子公司或部门"></Input>
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
              <InputNumber :min="0" :step="1.2" v-model="form.amount" placeholder="必填项"></InputNumber>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12" v-if="showLandMoney">
            <FormItem label="土地费用(万元)" prop="land_amount">
              <InputNumber :min="0" :step="1.2" v-model="form.land_amount" placeholder=""></InputNumber>
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
            <FormItem label="国民经济计划分类" prop="nep_type" :rules="isGcRole">
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
          <Col span="24">
            <FormItem label="项目地图">
              <Row v-if="addMap">
                <Col span="24">
                  <Button type="info" long @click="chooseArea" icon="md-add">绘制地图</Button>
                </Col>
              </Row>
              <Row v-if="showMap">
                <Col span="24">
                  <div id="addViewMap" style="height:300px;width:100%;"></div>
                </Col>
                <Col span="24">
                  <Button type="primary" @click="editArea" long icon="ios-create" style="margin-top: 10px;">修改</Button>
                </Col>
              </Row>
            </FormItem>
            <!--            <FormItem-->
            <!--              v-for="(item, index) in form.positions"-->
            <!--              v-if="item.status"-->
            <!--              :key="index"-->
            <!--              :label="'项目轮廓坐标 ' + item.index"-->
            <!--              :prop="'positions.' + index + '.value'">-->
            <!--              <Row>-->
            <!--                <Col span="18">-->
            <!--                  <Input type="text" v-model="item.value" placeholder="请输入坐标"></Input>-->
            <!--                </Col>-->
            <!--                <Col span="4" offset="1">-->
            <!--                  <Button @click="handleRemove(index)">移除</Button>-->
            <!--                </Col>-->
            <!--              </Row>-->
            <!--            </FormItem>-->
            <!--            <FormItem>-->
            <!--              <Row>-->
            <!--                <Col span="12">-->
            <!--                  <Button type="dashed" long @click="handleAdd" icon="md-add">添加坐标点</Button>-->
            <!--                </Col>-->
            <!--              </Row>-->
            <!--            </FormItem>-->
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <FormItem label="项目概况(建设规模及主要内容)" prop="description">
              <Input v-model="form.description" type="textarea" :rows="4" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Divider v-if="planDisplay"><h4>项目投资计划</h4></Divider>
        <div v-for="(item, index_t) in form.projectPlan">
          <Divider orientation="left"><h5 style="color: #2d8cf0;">{{item.date}}年项目投资计划</h5></Divider>
          <Row>
            <Col span="24">
              <FormItem
                label="计划投资金额(万元)"
                :prop="'projectPlan.' + index_t + '.amount'"
                :rules="item.role">
                <InputNumber :min="0" :step="1.2" v-model="item.amount" :placeholder="item.placeholder"></InputNumber>
              </FormItem>
            </Col>
            <Col span="24">
              <FormItem
                label="计划形象进度"
                :prop="'projectPlan.' + index_t + '.image_progress'"
                :rules="item.imageProgress">
                <Input v-model="item.image_progress" type="textarea" :rows="1" :placeholder="item.placeholder"></Input>
              </FormItem>
            </Col>
            <Col span="24" v-if="item.date === currentYear">
              <FormItem label="月计划投资金额合计(万元)"
                        :prop="'projectPlan.' + index_t + '.total_count_amount'">
                <Input :min="0" :step="1.2" v-model="item.total_count_amount" placeholder="月计划投资金额合计" value="0"
                       readonly></Input>
              </FormItem>
            </Col>
          </Row>
          <Row style="padding-left: 25px;">
            <Col span="3">
              <Input type="text" value="月份" class="borderNone"/>
            </Col>
            <Col span="4">
              <FormItem
                label="计划投资金额(万元)"
                :required="item.required"
                class="required-field"
                style="margin-bottom:0;">
              </FormItem>
            </Col>
            <Col span="17">
              <FormItem
                label="计划形象进度"
                :required="item.required"
                class="required-field"
                style="margin-bottom:0;">
              </FormItem>
            </Col>
            <div v-for="(ite, index) in item.month">
              <Col span="3">
                <FormItem class="monthAmount">
                  <Input type="text" placeholder="" v-model="ite.date + '月'" readonly class="monthInput"/>
                </FormItem>
              </Col>
              <Col span="4">
                <FormItem
                  :prop="'projectPlan.' + index_t + '.month.' + index + '.amount'"
                  :rules="ite.monthRole"
                  class="monthAmount">
                  <InputNumber
                    :min="0" :step="1.2" v-model="ite.amount" @on-blur="totalAmount(index_t,index)"
                    :placeholder="ite.monthPlaceholder"
                    class="monthInput">
                  </InputNumber>
                </FormItem>
              </Col>
              <Col span="17">
                <FormItem
                  :prop="'projectPlan.' + index_t + '.month.' + index + '.image_progress'"
                  :rules="ite.monthImageProgress"
                  class="monthAmount">
                  <Input type="textarea" :rows="1" placeholder="请输入..." v-model="ite.image_progress"
                         class="monthInput"/>
                </FormItem>
              </Col>
            </div>
          </Row>
        </div>
      </Form>
      <div slot="footer">
        <Button @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
        <Button type="primary" @click="handleSubmit('formValidate')" :loading="loading">保存</Button>
      </div>
    </Modal>
    <Modal v-model="modal11" fullscreen>
      <p slot="header" style="text-align:center;">
        <span>选取坐标</span>
      </p>
      <div id="map" :style="mapStyle"></div>
      <div slot="footer">
        <ButtonGroup>
          <Button type="primary" @click="isEdit('enable')">开启编辑</Button>
          <Button type="info" @click="isEdit('disable')">关闭编辑</Button>
        </ButtonGroup>
        <Button type="error" @click="clearAll()" style="margin-left: 8px">清除重绘</Button>
        <Button type="success" @click="complete()">完成</Button>
      </div>
    </Modal>
    <Modal v-model="modal222" fullscreen>
      <p slot="header" style="text-align:center;">
        <span>选取坐标</span>
      </p>
      <div id="mapEdit" :style="mapStyle"></div>
      <div slot="footer">
        <ButtonGroup>
          <Button type="primary" @click="isEdit('enable')">开启编辑</Button>
          <Button type="info" @click="isEdit('disable')">关闭编辑</Button>
        </ButtonGroup>
        <!--        <Input id="suggestId" search placeholder="Enter something..." />-->
        <Button type="error" @click="clearEditAll()" style="margin-left: 8px">清除重绘</Button>
        <Button type="success" @click="completeEdit()">完成</Button>
      </div>
    </Modal>
    <Modal
      :mask-closable="false"
      v-model="editModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="修改项目">
      <Alert type="error" show-icon v-if="openErrorAlert">
        审核意见
        <span slot="desc">
              {{editForm.reason}}
          </span>
      </Alert>
      <Form ref="editFormValidate" :model="editForm" :rules="ruleValidate" :label-width="143">
        <Divider><h4>基础信息</h4></Divider>
        <Row>
          <Col span="12">
            <FormItem label="项目名称" prop="title">
              <Input v-model="editForm.title" placeholder="必填项" :disabled="isAdjustReadOnly"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目编号" prop="num">
              <Input v-model="editForm.num" placeholder="由发展经营部统一填写" :disabled="isAdjustReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目类型" prop="type">
              <Select v-model="editForm.type" :disabled="isAdjustReadOnly">
                <Option v-for="item in dict.type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="editForm.subject" placeholder="必填项" :disabled="isAdjustReadOnly"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="建设性质" prop="build_type">
              <Select v-model="editForm.build_type" :disabled="isAdjustReadOnly">
                <Option v-for="item in dict.build_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="承建单位" prop="unit">
              <Input v-model="editForm.unit" readonly placeholder="子公司或部门" :disabled="isAdjustReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="资金来源" prop="money_from">
              <Select v-model="editForm.money_from" :disabled="isAdjustReadOnly">
                <Option v-for="item in dict.money_from" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目金额(万元)" prop="amount">
              <InputNumber :min="0" :step="1.2" v-model="editForm.amount" placeholder="" :disabled="isAdjustReadOnly">
              </InputNumber>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12" v-if="showLandMoney">
            <FormItem label="土地费用(万元)" prop="land_amount">
              <InputNumber :step="1.2" :min="0" v-model="editForm.land_amount" placeholder=""
                           :disabled="isAdjustReadOnly">
              </InputNumber>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目状态" prop="status">
              <Select v-model="editForm.status" :disabled="isAdjustReadOnly">
                <Option v-for="item in dict.status" :value="item.value" :key="item.value">{{item.title}}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目标识(是否为国民经济计划)" prop="is_gc">
              <Select v-model="editForm.is_gc" @on-change="onEditIsGcChange" :disabled="isAdjustReadOnly">
                <Option v-for="item in dict.is_gc" :value="item.value" :key="item.value">{{item.title}}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="国民经济计划分类" prop="nep_type" :rules="isGcRole">
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
                          v-model="editForm.plan_start_at" :disabled="isAdjustReadOnly"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="计划结束时间" prop="plan_end_at">
              <DatePicker type="month" placeholder="结束时间" format="yyyy年MM月"
                          v-model="editForm.plan_end_at" @on-change="buildEditYearPlan"
                          :disabled="isAdjustReadOnly"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <FormItem label="项目地图">
              <Row v-if="editAddMap">
                <Col span="24">
                  <Button type="info" long @click="chooseEditArea" icon="md-add">绘制地图</Button>
                </Col>
              </Row>
              <Row v-if="editShowMap">
                <Col span="24">
                  <div id="editMap" style="height:300px;width:100%;"></div>
                </Col>
                <Col span="24">
                  <Button type="primary" @click="editEditArea" long icon="ios-create" style="margin-top: 10px;">修改
                  </Button>
                </Col>
              </Row>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <FormItem label="项目概况(建设规模及主要内容)" prop="description">
            <Input v-model="editForm.description" type="textarea" :rows="4" placeholder="请输入..."
                   :disabled="isAdjustReadOnly"></Input>
          </FormItem>
        </Row>
        <Divider><h4>投资计划</h4></Divider>
        <div v-for="(item, index_t) in editForm.projectPlan">
          <Divider orientation="left"><h5 style="color: #2d8cf0;">{{item.date}}年项目投资计划</h5></Divider>
          <Row>
            <Col span="24">
              <FormItem
                label="计划投资金额(万元)"
                :prop="'projectPlan.' + index_t + '.amount'"
                :rules="item.role">
                <InputNumber :min="1" :step="1.2" v-model="item.amount" :placeholder="item.placeholder"
                             :disabled="isReadOnly">
                </InputNumber>
              </FormItem>
            </Col>
            <Col span="24">
              <FormItem
                label="计划形象进度"
                :prop="'projectPlan.' + index_t + '.image_progress'"
                :rules="item.imageProgress">
                <Input v-model="item.image_progress" type="textarea" :rows="1" placeholder="请输入..."
                       :readonly="isReadOnly"></Input>
              </FormItem>
            </Col>
            <Col span="24" v-if="item.date === currentYear">
              <FormItem label="月计划投资金额合计(万元)"
                        :prop="'projectPlan.' + index_t + '.total_count_amount'">
                <Input :min="0" :step="1.2" v-model="item.total_count_amount" placeholder="月计划投资金额合计" value="0"
                       readonly></Input>
              </FormItem>
            </Col>
          </Row>
          <Row style="padding-left: 25px;">
            <Col span="3">
              <Input type="text" value="月份" class="borderNone"/>
            </Col>
            <Col span="4">
              <FormItem
                label="计划投资金额(万元)"
                class="required-field"
                :required="item.required"
                style="margin-bottom:0;">
              </FormItem>
            </Col>
            <Col span="17">
              <FormItem
                label="计划形象进度"
                class="required-field"
                :required="item.required"
                style="margin-bottom:0;">
              </FormItem>
            </Col>
            <div v-for="(ite, index) in item.month">
              <Col span="3">
                <FormItem class="monthAmount">
                  <Input type="text" placeholder="" v-model="ite.date + '月'" readonly class="monthInput"/>
                </FormItem>
              </Col>
              <Col span="4">
                <FormItem
                  :prop="'projectPlan.' + index_t + '.month.' + index + '.amount'"
                  :rules="ite.monthRole"
                  class="monthAmount">
                  <InputNumber
                    :min="0" :step="1.2" v-model="ite.amount" @on-blur="totalAmountE(index_t,index)"
                    :placeholder="ite.monthPlaceholder" :readonly="ite.monthReadonly"
                    class="monthInput">
                  </InputNumber>
                </FormItem>
              </Col>
              <Col span="17">
                <FormItem
                  :prop="'projectPlan.' + index_t + '.month.' + index + '.image_progress'"
                  :rules="ite.monthImageProgress"
                  class="monthAmount">
                  <Input type="textarea" :rows="1" placeholder="请输入..." :readonly="ite.monthProgressReadonly"
                         v-model="ite.image_progress" class="monthInput"/>
                </FormItem>
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
      :mask-closable="false"
      v-model="previewModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="查看项目">
      <Alert type="error" show-icon v-if="openErrorAlert">
        审核意见
        <span slot="desc">
              {{previewForm.reason}}
          </span>
      </Alert>
      <Form ref="previewFormValidate" :model="previewForm" :label-width="143">
        <Divider><h4>基础信息</h4></Divider>
        <Row>
          <Col span="12">
            <FormItem label="项目名称" prop="title">
              <Input v-model="previewForm.title" placeholder="" :readonly="isReadOnly"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目编号" prop="num">
              <Input v-model="previewForm.num" placeholder="" :readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目类型" prop="type">
              <Input v-model="previewForm.type" placeholder="" :readonly="isReadOnly"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="previewForm.subject" placeholder="" :readonly="isReadOnly"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="建设性质" prop="build_type">
              <Input v-model="previewForm.build_type" placeholder="" :readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="承建单位" prop="unit">
              <Input v-model="previewForm.unit" placeholder="" :readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="资金来源" prop="money_from">
              <Input v-model="previewForm.money_from" placeholder="" :readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目金额(万元)" prop="amount">
              <Input v-model="previewForm.amount" placeholder="" :readonly="isReadOnly"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12" v-if="showLandMoney">
            <FormItem label="土地费用(万元)" prop="land_amount">
              <Input v-model="previewForm.land_amount" placeholder="" :readonly="isReadOnly"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目状态" prop="status">
              <Input v-model="previewForm.status" placeholder="" :readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目标识(是否为国民经济计划)" prop="is_gc">
              <Input v-model="previewForm.is_gc" placeholder="" :readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="国民经济计划分类" prop="nep_type">
              <Input v-model="previewForm.nep_type" placeholder="" :readonly="isReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="计划开始时间" prop="plan_start_at">
              <DatePicker type="month" :readonly="isReadOnly" placeholder="" format="yyyy年MM月"
                          v-model="previewForm.plan_start_at"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="计划结束时间" prop="plan_end_at">
              <DatePicker type="month" :readonly="isReadOnly" @on-change="buildYearPlan" placeholder=""
                          format="yyyy年MM月" v-model="previewForm.plan_end_at"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <FormItem label="项目地图">
              <Row v-if="noMap">
                <Col span="24">
                  <Alert show-icon>暂无地图</Alert>
                </Col>
              </Row>
              <Row v-if="haveMap">
                <Col span="24">
                  <div id="onlyView" style="height:300px;width:100%;"></div>
                </Col>
              </Row>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <FormItem label="项目概况(建设规模及主要内容)" prop="description">
            <Input v-model="previewForm.description" type="textarea" :rows="4" placeholder="" :readonly="isReadOnly">
            </Input>
          </FormItem>
        </Row>
        <Divider><h4>投资计划</h4></Divider>
        <div v-for="(item, index) in previewForm.projectPlan">
          <Divider orientation="left"><h5 style="color: #2d8cf0;">{{item.date}}年项目投资计划</h5></Divider>
          <Row>
            <Col span="24">
              <FormItem
                label="计划投资金额(万元)"
                :prop="'projectPlan.' + index + '.amount'">
                <Input v-model="item.amount" placeholder="" :readonly="isReadOnly"/>
              </FormItem>
            </Col>
            <Col span="24">
              <FormItem
                label="计划形象进度"
                :prop="'projectPlan.' + index + '.image_progress'">
                <Input v-model="item.image_progress" type="textarea" :rows="1" placeholder="" :readonly="isReadOnly">
                </Input>
              </FormItem>
            </Col>
            <Col span="24" v-if="item.date === currentYear">
              <FormItem label="月计划投资金额合计(万元)"
                        :prop="'projectPlan.' + index + '.total_count_amount'">
                <Input v-model="item.total_count_amount" placeholder="月计划投资金额合计" value="0" readonly></Input>
              </FormItem>
            </Col>
          </Row>
          <Row style="padding-left: 25px;">
            <Col span="3">
              <Input type="text" value="月份" class="borderNone"/>
            </Col>
            <Col span="4">
              <Input type="text" value="计划投资金额(万元)" class="borderNone"/>
            </Col>
            <Col span="17">
              <Input type="text" value="计划形象进度" class="borderNone"/>
            </Col>
            <div v-for="(ite, index) in item.month">
              <Col span="3">
                <Input type="text" v-model="ite.date + '月'" readonly class="monthInput"/>
              </Col>
              <Col span="4">
                <Input type="text" placeholder="" v-model="ite.amount" readonly class="monthInput"/>
              </Col>
              <Col span="17">
                <Input type="textarea" :rows="1" placeholder="" v-model="ite.image_progress" readonly
                       class="monthInput"/>
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
    <Modal
      :mask-closable="false"
      v-model="reasonModal"
      title="请简要说明原因"
      @on-cancel="cancelReasonForm"
    >
      <Form ref="reasonForm" :model="reasonForm">
        <FormItem prop="reason">
          <Input type="textarea" size="large" v-model="reasonForm.reason" :rows="4" placeholder="请输入"/>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="primary" @click="reasonAudit()" :loading="reasonAuditLoading">确定</Button>
      </div>
    </Modal>
    <Modal
      v-model="projectModal"
      title="是否调整所选项目"
    >
      <div v-for="(item,index) in projectAdjustmentIds">
        <p>{{item.title}}</p>
      </div>
      <div slot="footer">
        <Button type="text" @click="projectAdjustmentOk()">确定</Button>
        <Button type="primary" @click="projectAdjustmentCancel()">取消</Button>
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
    auditProject,
    toAudit,
    projectDelete,
    projectAdjustment
  } from '../../../api/project';
  import {getAllDepartment, loadClassDepartment} from '../../../api/system';
  import './projects.css'
  import '../../../../../../public/assets/css/DrawingManager_min.css';
  import $ from 'jquery';

  export default {
    data: function () {
      return {
        dataDep1: [],
        clickSearch: false,
        currentYear: 2019,
        isShowAdjustmentBtn: false,
        isGcRole: {required: false},
        noMap: false,
        haveMap: false,
        modal11: false,
        modal222: false,
        projectModal: false,
        dataCount: 0,   // 总条数
        nowData: [],
        isShowButton: false,
        showExportButton: false,
        reasonModal: false,
        reasonAuditLoading: false,
        dropDownContent: '展开',
        drop: false,
        dropDownIcon: "ios-arrow-down",
        isReadOnly: false,
        isAdjustReadOnly: false,
        btnDisable: true,
        exportBtnDisable: true,
        projectBtnDisable: true,
        editFormLoading: false,
        planDisplay: false,
        projectAdjustmentIds: [],
        reasonForm: {
          reason: ''
        },
        openErrorAlert: false,
        searchForm: {
          title: '',
          subject: '',
          department_id: [],
          num: '',
          type: '',
          build_type: '',
          money_from: '',
          is_gc: '',
          nep_type: '',
          status: '',
          is_audit: '',
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        columns: [
          {
            type: 'index2',
            width: 70,
            align: 'center',
            fixed: 'left',
            render: (h, params) => {
              return h('span', params.index + (this.searchForm.pageNumber - 1) * this.searchForm.pageSize + 1);
            }
          },
          {
            title: '项目名称',
            key: 'title',
            width: 220,
            fixed: 'left'
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
          {
            title: '项目编号',
            key: 'num',
            width: 210
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
              delButton = this.office === 0 ? !((params.row.is_audit === 2 || params.row.is_audit === 4) && params.row.audited === null) : true;
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
                      let _this = this;
                      this.showAuditButton = this.office === 1 ? params.row.is_audit === 0 : false;
                      this.previewForm = params.row;
                      this.formId = params.row.id;
                      this.isReadOnly = true;
                      this.openErrorAlert = (this.previewForm.reason !== '' && this.previewForm.is_audit === 2);
                      this.previewModal = true;

                      this.previewForm.projectPlan.forEach(function (row, index) {
                        let total_count_amount = 0;
                        row.month.forEach(function (e) {
                          total_count_amount += parseFloat(_this.clearComma(e.amount));
                        });
                        if (isNaN(total_count_amount)) {
                          row.total_count_amount = 0;
                        } else {
                          row.total_count_amount = total_count_amount.toLocaleString('zh', {
                            style: 'decimal',
                            minimumFractionDigits: 2,
                            useGrouping: true
                          });
                        }

                      });
                      if (this.previewForm.center_point && this.previewForm.positions) {
                        this.haveMap = true;
                        this.noMap = false;
                        this.onlyShowArea();
                      } else {
                        this.haveMap = false;
                        this.noMap = true;
                      }
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
                        if ((typeof this.editForm.plan_start_at === 'string') && this.editForm.plan_start_at.constructor === String) {
                          this.editForm.plan_start_at = new Date(Date.parse(this.editForm.plan_start_at));
                        }
                        if ((typeof this.editForm.plan_end_at === 'string') && this.editForm.plan_end_at.constructor === String) {
                          this.editForm.plan_end_at = new Date(Date.parse(this.editForm.plan_end_at));
                        }
                        if (params.row.is_audit === 3) {
                          this.addNepDisabled = params.row.is_audit === 3;
                        } else {
                          this.addNepDisabled = this.editForm.is_gc !== 1;
                          if (this.addNepDisabled) {
                            this.editForm.nep_type = '';
                          }
                        }
                        this.editForm.projectPlan.forEach(function (row, index) {
                          let total_count_amount = 0;
                          row.month.forEach(function (e) {
                            total_count_amount = parseFloat(total_count_amount) + parseFloat(e.amount);
                          });
                          if (isNaN(total_count_amount)) {
                            row.total_count_amount = 0;
                          } else {
                            row.total_count_amount = total_count_amount.toFixed(2);
                          }
                          let CurrentDate = new Date();
                          let CurrentYear = CurrentDate.getFullYear();
                          let CurrentMonth = CurrentDate.getMonth() + 1;
                          // 如果是当年，年度计划和月度计划都为必填
                          if (row.date === CurrentYear) {
                            row.role = {required: true, message: '计划投资金额不能为空', trigger: 'blur', type: 'number'};
                            row.imageProgress = {required: true, message: '计划形象进度不能为空', trigger: 'blur'};
                            row.placeholder = '必填项';
                            row.required = true;
                            if (row.month !== undefined) {
                              row.month.forEach(function (e) {
                                if (e.date < CurrentMonth) {
                                  e.monthReadonly = true;
                                  e.monthProgressReadonly = true;
                                } else {
                                  e.monthRole = {required: true, message: '不能为空', trigger: 'blur', type: 'number'};
                                  e.monthImageProgress = {required: true, message: '月计划形象进度不能为空', trigger: 'blur'};
                                  e.monthPlaceholder = '必填项';
                                }
                              });
                            }
                            // 如果是之前年，年度计划为必填，月度计划非必填
                          } else if (row.date < CurrentYear) {
                            row.role = {required: true, message: '计划投资金额不能为空', trigger: 'blur', type: 'number'};
                            row.imageProgress = {required: true, message: '计划形象进度不能为空', trigger: 'blur'};
                            row.placeholder = '必填项';
                            row.required = false;
                            if (row.month !== undefined) {
                              row.month.forEach(function (e) {
                                e.monthReadonly = true;
                                e.monthProgressReadonly = true;
                                // e.monthRole = {required: false, type: 'number'};
                                // e.monthImageProgress = {required: false};
                                // e.monthPlaceholder = '非必填';
                              });
                            }
                          } else {
                            row.role = {required: false, type: 'number'};
                            row.imageProgress = {required: false};
                            row.placeholder = '非必填';
                            row.required = false;
                            if (row.month !== undefined) {
                              row.month.forEach(function (e) {
                                e.monthRole = {required: false, type: 'number'};
                                e.monthImageProgress = {required: false};
                                e.monthPlaceholder = '非必填';
                              });
                            }
                          }
                        });
                        this.formId = params.row.id;
                        this.isAdjustReadOnly = params.row.is_audit === 3;
                        this.isReadOnly = false;
                        this.openErrorAlert = (this.editForm.reason !== '' && this.editForm.is_audit === 2);
                        this.editModal = true;

                        if (this.editForm.center_point && this.editForm.positions) {
                          this.editAddMap = false;
                          this.editShowMap = true;
                          this.editForm.center_point = JSON.parse(this.editForm.center_point);
                          this.editForm.positions = JSON.parse(this.editForm.positions);
                          this.showEditArea();
                        } else {
                          this.editAddMap = true;
                          this.editShowMap = false;
                        }
                        // this.editFormLoading = false;
                      });
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
                        content: "您确认要删除这个项目？",
                        onOk: () => {
                          projectDelete({id: params.row.id}).then(res => {
                            if (res.result === true) {
                              this.$Message.success("删除成功");
                              this.init();
                            } else {
                              this.$Message.error("项目不能删除");
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
        showLandMoney: false,
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
          amount: null,
          land_amount: null,
          is_gc: '',
          nep_type: '',
          plan_start_at: '',
          plan_end_at: '',
          center_point: {},
          description: '',
          positions: [],
          projectPlan: [
            {
              date: '2019',
              amount: null,
              image_progress: '',
              total_count_amount: null,
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
        project_list: [],
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
            {required: true, message: '项目金额不能为空', trigger: 'blur', type: 'number'}
          ],
          is_gc: [
            {required: true, message: '项目标识不能为空', trigger: 'change', type: 'number'}
          ],
          plan_start_at: [
            {required: true, message: '计划开始时间不能为空', trigger: 'change', type: 'date'},
          ],
          plan_end_at: [
            {required: true, message: '计划结束时间不能为空', trigger: 'change', type: 'date'},
          ],
        },
        overlays: [],
        drawingManager: {},
        map: {},
        addViewMap: {},
        addMap: true,
        editView: {},
        editViewMap: {},
        editAddMap: false,
        editShowMap: false,
        showMap: false,
        mapStyle: {
          height: '',
          width: ''
        },
        departmentIds: []
      }
    },
    methods: {
      init() {
        let date = new Date();
        this.currentYear = date.getFullYear();
        this.office = this.$store.getters.user.office;
        if (this.office === 2) {
          this.showLandMoney = true;
          this.isShowAdjustmentBtn = true;
          if (this.columns[0].type !== 'selection') {
            this.columns.unshift(
              {
                type: 'selection',
                width: 50,
                align: 'center',
                fixed: 'left',
                display: 'none'
              },
            );
          }
        }
        this.isShowButton = this.office === 0;
        this.showExportButton = !(this.office === 0);
        this.$refs.formValidate.resetFields();
        this.getDictData();
        this.getProject();
        getAllDepartment().then(res => {
          if (res.result) {
            this.departmentIds = res.result;
          }
        });
        loadClassDepartment().then(res => {
          this.dataDep1 = res;
        });
      },
      loadStaticMapData(fileName) {
        let basePath = window.document.location.host;
        this.$http.get('http://' + basePath + '/assets/json/' + fileName).then(response => {
          if (fileName === 'xingzheng.geo.json') {
            let data = response.body.features[0];
            let polygonArr = data.geometry.coordinates[0];
            let polygon = '';
            polygonArr.forEach(function (e) {
              polygon += e.join(',') + ';';
            });
            let ply = new BMap.Polygon(polygon, data.properties);
            this.map.addOverlay(ply);
          } else {
            let polylineArr = response.body.features;
            let _this = this;
            polylineArr.forEach(function (e) {
              let polyline = '';
              e.geometry.coordinates.forEach(function (el) {
                polyline += el.join(',') + ';';
              });
              let poly = new BMap.Polyline(polyline.substr(0, polyline.length - 1), e.properties);
              _this.map.addOverlay(poly);
            });
          }
        }, response => {
          this.$Message.error('据读取失败!');
        });
      },
      loadEditStaticMapData(fileName) {
        let basePath = window.document.location.host;
        this.$http.get('http://' + basePath + '/assets/json/' + fileName).then(response => {
          if (fileName === 'xingzheng.geo.json') {
            let data = response.body.features[0];
            let polygonArr = data.geometry.coordinates[0];
            let polygon = '';
            polygonArr.forEach(function (e) {
              polygon += e.join(',') + ';';
            });
            let ply = new BMap.Polygon(polygon, data.properties);
            this.editViewMap.addOverlay(ply);
          } else {
            let polylineArr = response.body.features;
            let _this = this;
            polylineArr.forEach(function (e) {
              let polyline = '';
              e.geometry.coordinates.forEach(function (el) {
                polyline += el.join(',') + ';';
              });
              let poly = new BMap.Polyline(polyline.substr(0, polyline.length - 1), e.properties);
              _this.editViewMap.addOverlay(poly);
            });
          }
        }, response => {
          this.$Message.error('据读取失败!');
        });
      },
      createMap() {
        // enableMapClick: false 构造底图时，关闭底图可点功能
        this.map = new BMap.Map("map", {enableMapClick: false, mapType: BMAP_HYBRID_MAP});
        this.map.centerAndZoom(new BMap.Point(108.720027, 34.298497), 15);
        this.map.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
        this.map.addControl(new BMap.NavigationControl());
        this.map.addControl(new BMap.MapTypeControl({
          type: BMAP_MAPTYPE_CONTROL_HORIZONTAL, // 按钮水平方式展示，默认采用此类型展示
          mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP], // 控件展示的地图类型
          anchor: BMAP_ANCHOR_BOTTOM_RIGHT
        }));
        this.clearAll();
        // 加载行政区划
        this.loadStaticMapData('xingzheng.geo.json');
        // 加载路网
        this.loadStaticMapData('luwang.geo.json');
        console.log('加载鼠标绘制工具...');
        let _this = this;
        return new Promise((resolve, reject) => {
          let drawNode = document.createElement("script");
          drawNode.setAttribute("type", "text/javascript");
          drawNode.setAttribute("src", '/assets/js/DrawingManager_min.js');
          document.body.appendChild(drawNode);
          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("鼠标绘制工具加载失败...");
            }
            // 加载成功
            if (typeof BMapLib !== "undefined") {
              resolve(BMapLib);
              clearInterval(interval);
              console.log("鼠标绘制工具加载成功...");
              // let arr = [];
              let overlaycomplete = function (e) {
                e.overlay.drawingMode = e.drawingMode;
                if (e.drawingMode === 'marker') {
                  $('.BMapLib_marker_hover, .BMapLib_marker').css('display', 'none');
                }
                _this.overlays.push(e.overlay);
                _this.Editing('enable');
              };
              let polygonStyleOptions = {
                strokeColor: "#f44336", // 边线颜色。
                fillColor: "#f44336", // 填充颜色。当参数为空时，圆形将没有填充效果。
                fillOpacity: 0.2, // 填充的透明度，取值范围0 - 1。
                strokeWeight: 4, // 边线的宽度，以像素为单位。
                strokeOpacity: 0.8, // 边线透明度，取值范围0 - 1。
                strokeStyle: 'solid' // 边线的样式，solid或dashed。
              };
              let polylineStyleOptions = {
                strokeColor: "#2196f3", // 边线颜色。
                strokeWeight: 4, // 边线的宽度，以像素为单位。
                strokeOpacity: 0.8, // 边线透明度，取值范围0 - 1。
                strokeStyle: 'solid' // 边线的样式，solid或dashed。
              };
              //实例化鼠标绘制工具
              this.drawingManager = new BMapLib.DrawingManager(this.map, {
                isOpen: false, // 是否开启绘制模式
                enableDrawingTool: true, // 是否显示工具栏
                drawingToolOptions: {
                  anchor: BMAP_ANCHOR_TOP_RIGHT, // 位置
                  offset: new BMap.Size(5, 5), // 偏离值
                  drawingModes: [BMAP_DRAWING_MARKER, BMAP_DRAWING_POLYLINE, BMAP_DRAWING_POLYGON], // 设置只显示画折线和多边形
                },
                polylineOptions: polylineStyleOptions, // 折线样式
                polygonOptions: polygonStyleOptions, // 多边形样式
              });

              // 添加鼠标绘制工具监听事件，用于获取绘制结果
              this.drawingManager.addEventListener('overlaycomplete', overlaycomplete);
              this.drawingManager.addEventListener("markercomplete", function (e, overlay) {
                _this.drawingManager.setDrawingMode('hander');
              });

              // this.createMap();
            }
            timeout += 1;
          }, 500);
        });
      },
      editCreateMap() {
        this.editViewMap = new BMap.Map("mapEdit", {enableMapClick: false, mapType: BMAP_HYBRID_MAP});
        this.editViewMap.centerAndZoom(new BMap.Point(108.720027, 34.298497), 15);
        this.editViewMap.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
        this.editViewMap.addControl(new BMap.NavigationControl());
        this.editViewMap.addControl(new BMap.MapTypeControl({
          type: BMAP_MAPTYPE_CONTROL_HORIZONTAL, // 按钮水平方式展示，默认采用此类型展示
          mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP], // 控件展示的地图类型
          anchor: BMAP_ANCHOR_BOTTOM_RIGHT
        }));
        this.clearEditAll();
        // 加载行政区划
        this.loadEditStaticMapData('xingzheng.geo.json');
        // 加载路网
        this.loadEditStaticMapData('luwang.geo.json');
        console.log('加载鼠标绘制工具...');
        let _this = this;
        return new Promise((resolve, reject) => {
          let drawNode = document.createElement("script");
          drawNode.setAttribute("type", "text/javascript");
          drawNode.setAttribute("src", '/assets/js/DrawingManager_min.js');
          document.body.appendChild(drawNode);
          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("鼠标绘制工具加载失败...");
            }
            // 加载成功
            if (typeof BMapLib !== "undefined") {
              resolve(BMapLib);
              clearInterval(interval);
              console.log("鼠标绘制工具加载成功...");
              let overlaycomplete = function (e) {
                e.overlay.drawingMode = e.drawingMode;
                if (e.drawingMode === 'marker') {
                  $('.BMapLib_marker_hover, .BMapLib_marker').css('display', 'none');
                }
                _this.overlays.push(e.overlay);
                _this.Editing('enable');
              };
              let polygonStyleOptions = {
                strokeColor: "#f44336", // 边线颜色。
                fillColor: "#f44336", // 填充颜色。当参数为空时，圆形将没有填充效果。
                fillOpacity: 0.2, // 填充的透明度，取值范围0 - 1。
                strokeWeight: 4, // 边线的宽度，以像素为单位。
                strokeOpacity: 0.8, // 边线透明度，取值范围0 - 1。
                strokeStyle: 'solid' // 边线的样式，solid或dashed。
              };
              let polylineStyleOptions = {
                strokeColor: "#2196f3", // 边线颜色。
                strokeWeight: 4, // 边线的宽度，以像素为单位。
                strokeOpacity: 0.8, // 边线透明度，取值范围0 - 1。
                strokeStyle: 'solid' // 边线的样式，solid或dashed。
              };
              //实例化鼠标绘制工具
              this.drawingManager = new BMapLib.DrawingManager(this.editViewMap, {
                isOpen: false, // 是否开启绘制模式
                enableDrawingTool: true, // 是否显示工具栏
                drawingToolOptions: {
                  anchor: BMAP_ANCHOR_TOP_RIGHT, // 位置
                  offset: new BMap.Size(5, 5), // 偏离值
                  drawingModes: [BMAP_DRAWING_MARKER, BMAP_DRAWING_POLYLINE, BMAP_DRAWING_POLYGON], // 设置只显示画折线和多边形
                },
                polylineOptions: polylineStyleOptions, // 折线样式
                polygonOptions: polygonStyleOptions, // 多边形样式
              });

              // 添加鼠标绘制工具监听事件，用于获取绘制结果
              this.drawingManager.addEventListener('overlaycomplete', overlaycomplete);
              this.drawingManager.addEventListener("markercomplete", function (e, overlay) {
                _this.drawingManager.setDrawingMode('hander');
              });
            }
            timeout += 1;
          }, 500);
        });
      },
      chooseArea() {
        $("#map").empty();
        this.modal11 = true;
        console.log("初始化百度地图脚本...");
        const AK = "rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu";
        const apiVersion = "3.0";
        const BMap_URL = "http://api.map.baidu.com/getscript?v=" + apiVersion + "&ak=" + AK;
        return new Promise((resolve, reject) => {
          // 插入script脚本DrawingManager_min
          let scriptNode = document.createElement("script");
          scriptNode.setAttribute("type", "text/javascript");
          scriptNode.setAttribute("src", BMap_URL);
          document.body.appendChild(scriptNode);

          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("百度地图脚本初始化失败...");
              this.$Message.error('地图加载失败，请检查网络连接是否正常!');
            }
            // 加载成功
            if (typeof BMap !== "undefined") {
              resolve(BMap);
              clearInterval(interval);
              console.log("百度地图脚本初始化成功...");
              this.createMap();
            }
            timeout += 1;
          }, 500);
        });
      },
      chooseEditArea() {
        $("#mapEdit").empty();
        this.modal222 = true;
        console.log("初始化百度地图脚本...");
        const AK = "rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu";
        const apiVersion = "3.0";
        const BMap_URL = "http://api.map.baidu.com/getscript?v=" + apiVersion + "&ak=" + AK;
        return new Promise((resolve, reject) => {
          // 插入script脚本DrawingManager_min
          let scriptNode = document.createElement("script");
          scriptNode.setAttribute("type", "text/javascript");
          scriptNode.setAttribute("src", BMap_URL);
          document.body.appendChild(scriptNode);

          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("百度地图脚本初始化失败...");
              this.$Message.error('地图加载失败，请检查网络连接是否正常!');
            }
            // 加载成功
            if (typeof BMap !== "undefined") {
              resolve(BMap);
              clearInterval(interval);
              console.log("百度地图脚本初始化成功...");
              this.editCreateMap();
            }
            timeout += 1;
          }, 500);
        });
      },
      showEditArea() {
        $("#editMap").empty();
        console.log("初始化百度地图脚本...");
        const AK = "rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu";
        const apiVersion = "3.0";
        const BMap_URL = "http://api.map.baidu.com/getscript?v=" + apiVersion + "&ak=" + AK;
        return new Promise((resolve, reject) => {
          // 插入script脚本DrawingManager_min
          let scriptNode = document.createElement("script");
          scriptNode.setAttribute("type", "text/javascript");
          scriptNode.setAttribute("src", BMap_URL);
          document.body.appendChild(scriptNode);

          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("百度地图脚本初始化失败...");
              this.$Message.error('地图加载失败，请检查网络连接是否正常!');
            }
            // 加载成功
            if (typeof BMap !== "undefined") {
              resolve(BMap);
              clearInterval(interval);
              console.log("百度地图脚本初始化成功...");

              this.editView = new BMap.Map("editMap", {enableMapClick: false, mapType: BMAP_HYBRID_MAP});
              // this.editView.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
              this.editView.addControl(new BMap.MapTypeControl({
                type: BMAP_MAPTYPE_CONTROL_HORIZONTAL, // 按钮水平方式展示，默认采用此类型展示
                mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP], // 控件展示的地图类型
                anchor: BMAP_ANCHOR_BOTTOM_RIGHT
              }));
              for (let i = 0; i < this.overlays.length; i++) {
                this.editView.removeOverlay(this.overlays[i]);
              }
              this.overlays.length = 0;
              $('.BMapLib_marker_hover, .BMapLib_marker').css('display', 'inherit');
              let allPoints = [];
              let markerPoints = this.editForm.center_point.coordinates;
              let mPoint = new BMap.Point(markerPoints.lng, markerPoints.lat);
              let marker = new BMap.Marker((mPoint), {
                strokeColor: "blue",
                strokeWeight: 3,
                strokeOpacity: 0.5,
                fillColor: ''
              });
              allPoints.push.apply(allPoints, [mPoint]);
              this.editView.addOverlay(marker);
              marker.drawingMode = 'marker';
              // this.overlays.push(marker);
              let _this = this;
              this.editForm.positions.forEach(function (e) {
                let points = [];
                e.coordinates.forEach(function (el) {
                  points.push(new BMap.Point(el.lng, el.lat));
                });
                if (e.drawingMode === 'polygon') {
                  let polygon = new BMap.Polygon(points, {
                    strokeColor: "blue",
                    strokeWeight: 3,
                    strokeOpacity: 0.5,
                    fillColor: ''
                  });
                  _this.editView.addOverlay(polygon);
                  polygon.drawingMode = 'polygon';
                  // _this.overlays.push(polygon);
                } else {
                  let polyline = new BMap.Polyline(points, {
                    strokeColor: "blue",
                    strokeWeight: 3,
                    strokeOpacity: 0.5,
                    fillColor: ''
                  });
                  _this.editView.addOverlay(polyline);
                  polyline.drawingMode = 'polyline';
                  // _this.overlays.push(polyline);
                }
                allPoints.push.apply(allPoints, points);
              });

              let view = this.editView.getViewport(eval(allPoints));
              let mapZoom = view.zoom;
              let centerPoint = view.center;
              this.editView.centerAndZoom(centerPoint, mapZoom);
            }
            timeout += 1;
          }, 500);
        });
      },
      onlyShowArea() {
        $("#onlyView").empty();
        console.log("初始化百度地图脚本...");
        const AK = "rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu";
        const apiVersion = "3.0";
        const BMap_URL = "http://api.map.baidu.com/getscript?v=" + apiVersion + "&ak=" + AK;
        return new Promise((resolve, reject) => {
          // 插入script脚本DrawingManager_min
          let scriptNode = document.createElement("script");
          scriptNode.setAttribute("type", "text/javascript");
          scriptNode.setAttribute("src", BMap_URL);
          document.body.appendChild(scriptNode);

          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("百度地图脚本初始化失败...");
              this.$Message.error('地图加载失败，请检查网络连接是否正常!');
            }
            // 加载成功
            if (typeof BMap !== "undefined") {
              resolve(BMap);
              clearInterval(interval);
              console.log("百度地图脚本初始化成功...");

              this.onlyView = new BMap.Map("onlyView", {enableMapClick: false, mapType: BMAP_HYBRID_MAP});
              // this.onlyView.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
              this.onlyView.addControl(new BMap.MapTypeControl({
                type: BMAP_MAPTYPE_CONTROL_HORIZONTAL, // 按钮水平方式展示，默认采用此类型展示
                mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP], // 控件展示的地图类型
                anchor: BMAP_ANCHOR_BOTTOM_RIGHT
              }));
              for (let i = 0; i < this.overlays.length; i++) {
                this.onlyView.removeOverlay(this.overlays[i]);
              }
              this.overlays.length = 0;
              $('.BMapLib_marker_hover, .BMapLib_marker').css('display', 'inherit');
              let allPoints = [];
              let markerPoints = JSON.parse(this.previewForm.center_point).coordinates;
              let mPoint = new BMap.Point(markerPoints.lng, markerPoints.lat);
              let marker = new BMap.Marker((mPoint), {
                strokeColor: "blue",
                strokeWeight: 3,
                strokeOpacity: 0.5,
                fillColor: ''
              });
              allPoints.push.apply(allPoints, [mPoint]);
              this.onlyView.addOverlay(marker);
              marker.drawingMode = 'marker';
              this.overlays.push(marker);
              let _this = this;
              JSON.parse(this.previewForm.positions).forEach(function (e) {
                let points = [];
                e.coordinates.forEach(function (el) {
                  points.push(new BMap.Point(el.lng, el.lat));
                });
                if (e.drawingMode === 'polygon') {
                  let polygon = new BMap.Polygon(points, {
                    strokeColor: "blue",
                    strokeWeight: 3,
                    strokeOpacity: 0.5,
                    fillColor: ''
                  });
                  _this.onlyView.addOverlay(polygon);
                  polygon.drawingMode = 'polygon';
                  _this.overlays.push(polygon);
                } else {
                  let polyline = new BMap.Polyline(points, {
                    strokeColor: "blue",
                    strokeWeight: 3,
                    strokeOpacity: 0.5,
                    fillColor: ''
                  });
                  _this.onlyView.addOverlay(polyline);
                  polyline.drawingMode = 'polyline';
                  _this.overlays.push(polyline);
                }
                allPoints.push.apply(allPoints, points);
              });

              let view = this.onlyView.getViewport(eval(allPoints));
              let mapZoom = view.zoom;
              let centerPoint = view.center;
              this.onlyView.centerAndZoom(centerPoint, mapZoom);
            }
            timeout += 1;
          }, 500);
        });
      },
      showEditAreaAgain() {
        $("#editMap").empty();
        console.log("初始化百度地图脚本...");
        const AK = "rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu";
        const apiVersion = "3.0";
        const BMap_URL = "http://api.map.baidu.com/getscript?v=" + apiVersion + "&ak=" + AK;
        return new Promise((resolve, reject) => {
          // 插入script脚本DrawingManager_min
          let scriptNode = document.createElement("script");
          scriptNode.setAttribute("type", "text/javascript");
          scriptNode.setAttribute("src", BMap_URL);
          document.body.appendChild(scriptNode);

          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("百度地图脚本初始化失败...");
              this.$Message.error('地图加载失败，请检查网络连接是否正常!');
            }
            // 加载成功
            if (typeof BMap !== "undefined") {
              resolve(BMap);
              clearInterval(interval);
              console.log("百度地图脚本初始化成功...");

              this.editView = new BMap.Map("editMap", {enableMapClick: false, mapType: BMAP_HYBRID_MAP});
              // this.editView.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
              this.editView.addControl(new BMap.MapTypeControl({
                type: BMAP_MAPTYPE_CONTROL_HORIZONTAL, // 按钮水平方式展示，默认采用此类型展示
                mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP], // 控件展示的地图类型
                anchor: BMAP_ANCHOR_BOTTOM_RIGHT
              }));
              // this.clearEditAll();
              let allPoints = [];
              let markerPoints = this.editForm.center_point.coordinates;
              let mPoint = new BMap.Point(markerPoints.lng, markerPoints.lat);
              let marker = new BMap.Marker((mPoint), {
                strokeColor: "blue",
                strokeWeight: 3,
                strokeOpacity: 0.5,
                fillColor: ''
              });
              allPoints.push.apply(allPoints, [mPoint]);
              this.editView.addOverlay(marker);
              marker.drawingMode = 'marker';
              this.overlays.push(marker);
              let _this = this;
              this.editForm.positions.forEach(function (e) {
                let points = [];
                e.coordinates.forEach(function (el) {
                  points.push(new BMap.Point(el.lng, el.lat));
                });
                if (e.drawingMode === 'polygon') {
                  let polygon = new BMap.Polygon(points, {
                    strokeColor: "blue",
                    strokeWeight: 3,
                    strokeOpacity: 0.5,
                    fillColor: ''
                  });
                  _this.editView.addOverlay(polygon);
                  polygon.drawingMode = 'polygon';
                  _this.overlays.push(polygon);
                } else {
                  let polyline = new BMap.Polyline(points, {
                    strokeColor: "blue",
                    strokeWeight: 3,
                    strokeOpacity: 0.5,
                    fillColor: ''
                  });
                  _this.editView.addOverlay(polyline);
                  polyline.drawingMode = 'polyline';
                  _this.overlays.push(polyline);
                }
                allPoints.push.apply(allPoints, points);
              });
              let view = this.editView.getViewport(eval(allPoints));
              let mapZoom = view.zoom;
              let centerPoint = view.center;
              this.editView.centerAndZoom(centerPoint, mapZoom);
            }
            timeout += 1;
          }, 500);
        });
      },
      showAddMap(allPoints) {
        this.addViewMap = new BMap.Map("addViewMap", {enableMapClick: false, mapType: BMAP_HYBRID_MAP});
        // this.addViewMap.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
        this.addViewMap.addControl(new BMap.MapTypeControl({
          type: BMAP_MAPTYPE_CONTROL_HORIZONTAL, // 按钮水平方式展示，默认采用此类型展示
          mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP], // 控件展示的地图类型
          anchor: BMAP_ANCHOR_BOTTOM_RIGHT
        }));
        let view = this.addViewMap.getViewport(eval(allPoints));
        let mapZoom = view.zoom;
        let centerPoint = view.center;
        this.addViewMap.centerAndZoom(centerPoint, mapZoom);
        let _this = this;

        this.overlays.forEach(function (e) {
          let points = [];
          let overlay;
          if (e.drawingMode === 'marker') {
            overlay = e.point;
            let marker = new BMap.Marker(new BMap.Point(overlay.lng, overlay.lat));
            _this.addViewMap.addOverlay(marker);
          } else {
            overlay = e.getPath();
            overlay.forEach(function (el) {
              points.push(new BMap.Point(el.lng, el.lat));
            });
            if (e.drawingMode === 'polygon') {
              let polygon = new BMap.Polygon(points, {
                strokeColor: "blue",
                strokeWeight: 3,
                strokeOpacity: 0.5,
                fillColor: ''
              });
              _this.addViewMap.addOverlay(polygon);
            } else {
              let polyline = new BMap.Polyline(points, {
                strokeColor: "blue",
                strokeWeight: 3,
                strokeOpacity: 0.5,
                fillColor: ''
              });
              _this.addViewMap.addOverlay(polyline);
            }
          }
        });
      },
      clearAll() {
        for (let i = 0; i < this.overlays.length; i++) {
          this.map.removeOverlay(this.overlays[i]);
        }
        this.overlays.length = 0;
        $('.BMapLib_marker_hover, .BMapLib_marker').css('display', 'inherit');
      },
      clearEditAll() {
        for (let i = 0; i < this.overlays.length; i++) {
          this.editViewMap.removeOverlay(this.overlays[i]);
        }
        this.overlays.length = 0;
        $('.BMapLib_marker_hover, .BMapLib_marker').css('display', 'inherit');
      },
      complete() {
        let length = this.overlays.length;
        let allPoints = [];
        if (length === 0) {
          this.$Message.error('未选择任何区域或标点!');
        } else if (length === 1) {
          if (this.overlays[0].drawingMode !== 'marker') {
            this.$Message.error('请插入标注点!');
          } else {
            this.$Message.error('请选择项目区域!');
          }
        } else {
          this.form.center_point = {};
          this.form.positions = [];
          let points = [];
          for (let i = 0; i < length; i++) {
            let overlay;
            if (this.overlays[i].drawingMode === 'marker') {
              overlay = this.overlays[i].point;
              this.form.center_point = {
                "drawingMode": this.overlays[i].drawingMode,
                "coordinates": overlay
              };
              points.push(new BMap.Point(overlay.lng, overlay.lat));
            } else {
              overlay = this.overlays[i].getPath();
              this.form.positions.push({
                "drawingMode": this.overlays[i].drawingMode,
                "coordinates": overlay
              });
              overlay.forEach(function (e) {
                points.push(new BMap.Point(e.lng, e.lat));
              });
            }
            allPoints.push.apply(allPoints, points);
          }
          this.modal11 = false;
          this.$Message.success('地图绘制成功!');
          this.addMap = false;
          this.showMap = true;
          this.map = {};
          this.drawingManager = {};
          console.log("初始化百度地图脚本...");
          const AK = "rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu";
          const apiVersion = "3.0";
          const BMap_URL = "http://api.map.baidu.com/getscript?v=" + apiVersion + "&ak=" + AK;
          return new Promise((resolve, reject) => {
            // 插入script脚本DrawingManager_min
            let scriptNode = document.createElement("script");
            scriptNode.setAttribute("type", "text/javascript");
            scriptNode.setAttribute("src", BMap_URL);
            document.body.appendChild(scriptNode);

            // 等待页面加载完毕回调
            let timeout = 0;
            let interval = setInterval(() => {
              // 超时10秒加载失败
              if (timeout >= 20) {
                reject();
                clearInterval(interval);
                console.error("百度地图脚本初始化失败...");
                this.$Message.error('地图加载失败，请检查网络连接是否正常!');
              }
              // 加载成功
              if (typeof BMap !== "undefined") {
                resolve(BMap);
                clearInterval(interval);
                console.log("百度地图脚本初始化成功...");
                this.showAddMap(allPoints);
              }
              timeout += 1;
            }, 500);
          });
        }
      },
      completeEdit() {
        let length = this.overlays.length;
        let allPoints = [];
        if (length === 0) {
          this.$Message.error('未选择任何区域或标点!');
        } else if (length === 1) {
          if (this.overlays[0].drawingMode !== 'marker') {
            this.$Message.error('请插入标注点!');
          } else {
            this.$Message.error('请选择项目区域!');
          }
        } else {
          this.editForm.center_point = {};
          this.editForm.positions = [];
          let points = [];
          for (let i = 0; i < length; i++) {
            let overlay;
            if (this.overlays[i].drawingMode === 'marker') {
              overlay = this.overlays[i].point;
              this.editForm.center_point = {
                "drawingMode": this.overlays[i].drawingMode,
                "coordinates": overlay
              };
              points.push(new BMap.Point(overlay.lng, overlay.lat));
            } else {
              overlay = this.overlays[i].getPath();
              this.editForm.positions.push({
                "drawingMode": this.overlays[i].drawingMode,
                "coordinates": overlay
              });
              overlay.forEach(function (e) {
                points.push(new BMap.Point(e.lng, e.lat));
              });
            }
            allPoints.push.apply(allPoints, points);
          }
          this.modal222 = false;
          this.$Message.success('地图绘制成功!');
          this.editAddMap = false;
          this.editShowMap = true;
          this.mapEdit = {};
          this.drawingManager = {};
          this.showEditAreaAgain();
        }
      },
      editArea() {
        $("#map").empty();
        this.modal11 = true;
        console.log("初始化百度地图脚本...");
        const AK = "rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu";
        const apiVersion = "3.0";
        const BMap_URL = "http://api.map.baidu.com/getscript?v=" + apiVersion + "&ak=" + AK;
        return new Promise((resolve, reject) => {
          // 插入script脚本DrawingManager_min
          let scriptNode = document.createElement("script");
          scriptNode.setAttribute("type", "text/javascript");
          scriptNode.setAttribute("src", BMap_URL);
          document.body.appendChild(scriptNode);

          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("百度地图脚本初始化失败...");
              this.$Message.error('地图加载失败，请检查网络连接是否正常!');
            }
            // 加载成功
            if (typeof BMap !== "undefined") {
              resolve(BMap);
              clearInterval(interval);
              console.log("百度地图脚本初始化成功...");
              this.editMap();
            }
            timeout += 1;
          }, 500);
        });
      },
      editEditArea() {
        $("#mapEdit").empty();
        this.modal222 = true;
        console.log("初始化百度地图脚本...");
        const AK = "rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu";
        const apiVersion = "3.0";
        const BMap_URL = "http://api.map.baidu.com/getscript?v=" + apiVersion + "&ak=" + AK;
        return new Promise((resolve, reject) => {
          // 插入script脚本DrawingManager_min
          let scriptNode = document.createElement("script");
          scriptNode.setAttribute("type", "text/javascript");
          scriptNode.setAttribute("src", BMap_URL);
          document.body.appendChild(scriptNode);

          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("百度地图脚本初始化失败...");
              this.$Message.error('地图加载失败，请检查网络连接是否正常!');
            }
            // 加载成功
            if (typeof BMap !== "undefined") {
              resolve(BMap);
              clearInterval(interval);
              console.log("百度地图脚本初始化成功...");
              this.editEditMap();
            }
            timeout += 1;
          }, 500);
        });
      },
      editMap() {
        // enableMapClick: false 构造底图时，关闭底图可点功能
        this.map = new BMap.Map("map", {enableMapClick: false, mapType: BMAP_HYBRID_MAP});
        this.map.centerAndZoom(new BMap.Point(108.720027, 34.298497), 15);
        this.map.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
        this.map.addControl(new BMap.NavigationControl());
        this.map.addControl(new BMap.MapTypeControl({
          type: BMAP_MAPTYPE_CONTROL_HORIZONTAL, // 按钮水平方式展示，默认采用此类型展示
          mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP], // 控件展示的地图类型
          anchor: BMAP_ANCHOR_BOTTOM_RIGHT
        }));
        this.clearAll();
        // 加载行政区划
        this.loadStaticMapData('xingzheng.geo.json');
        // 加载路网
        this.loadStaticMapData('luwang.geo.json');
        console.log('加载鼠标绘制工具...');
        let _this = this;
        return new Promise((resolve, reject) => {
          let drawNode = document.createElement("script");
          drawNode.setAttribute("type", "text/javascript");
          drawNode.setAttribute("src", '/assets/js/DrawingManager_min.js');
          document.body.appendChild(drawNode);
          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("鼠标绘制工具加载失败...");
            }
            // 加载成功
            if (typeof BMapLib !== "undefined") {
              resolve(BMapLib);
              clearInterval(interval);
              console.log("鼠标绘制工具加载成功...");
              // let arr = [];
              let overlaycomplete = function (e) {
                e.overlay.drawingMode = e.drawingMode;
                if (e.drawingMode === 'marker') {
                  $('.BMapLib_marker_hover, .BMapLib_marker').css('display', 'none');
                }
                _this.overlays.push(e.overlay);
                _this.Editing('enable');
              };
              let polygonStyleOptions = {
                strokeColor: "#f44336", // 边线颜色。
                fillColor: "#f44336", // 填充颜色。当参数为空时，圆形将没有填充效果。
                fillOpacity: 0.2, // 填充的透明度，取值范围0 - 1。
                strokeWeight: 4, // 边线的宽度，以像素为单位。
                strokeOpacity: 0.8, // 边线透明度，取值范围0 - 1。
                strokeStyle: 'solid' // 边线的样式，solid或dashed。
              };
              let polylineStyleOptions = {
                strokeColor: "#2196f3", // 边线颜色。
                strokeWeight: 4, // 边线的宽度，以像素为单位。
                strokeOpacity: 0.8, // 边线透明度，取值范围0 - 1。
                strokeStyle: 'solid' // 边线的样式，solid或dashed。
              };
              //实例化鼠标绘制工具
              this.drawingManager = new BMapLib.DrawingManager(this.map, {
                isOpen: false, // 是否开启绘制模式
                enableDrawingTool: true, // 是否显示工具栏
                drawingToolOptions: {
                  anchor: BMAP_ANCHOR_TOP_RIGHT, // 位置
                  offset: new BMap.Size(5, 5), // 偏离值
                  drawingModes: [BMAP_DRAWING_MARKER, BMAP_DRAWING_POLYLINE, BMAP_DRAWING_POLYGON], // 设置只显示画折线和多边形
                },
                polylineOptions: polylineStyleOptions, // 折线样式
                polygonOptions: polygonStyleOptions, // 多边形样式
              });

              // 添加鼠标绘制工具监听事件，用于获取绘制结果
              this.drawingManager.addEventListener('overlaycomplete', overlaycomplete);
              this.drawingManager.addEventListener("markercomplete", function (e, overlay) {
                _this.drawingManager.setDrawingMode('hander');
              });
              let allPoints = [];
              let markerPoints = this.form.center_point.coordinates;
              let marker = new BMap.Marker(new BMap.Point(markerPoints.lng, markerPoints.lat), {
                strokeColor: "blue",
                strokeWeight: 3,
                strokeOpacity: 0.5,
                fillColor: ''
              });
              allPoints.push.apply(allPoints, [new BMap.Point(markerPoints.lng, markerPoints.lat)]);
              this.map.addOverlay(marker);
              marker.drawingMode = 'marker';
              this.overlays.push(marker);
              let _this = this;
              this.form.positions.forEach(function (e) {
                let points = [];
                e.coordinates.forEach(function (el) {
                  points.push(new BMap.Point(el.lng, el.lat));
                });
                if (e.drawingMode === 'polygon') {
                  let polygon = new BMap.Polygon(points, {
                    strokeColor: "blue",
                    strokeWeight: 3,
                    strokeOpacity: 0.5,
                    fillColor: ''
                  });
                  _this.map.addOverlay(polygon);
                  polygon.drawingMode = 'polygon';
                  _this.overlays.push(polygon);
                } else {
                  let polyline = new BMap.Polyline(points, {
                    strokeColor: "blue",
                    strokeWeight: 3,
                    strokeOpacity: 0.5,
                    fillColor: ''
                  });
                  _this.map.addOverlay(polyline);
                  polyline.drawingMode = 'polyline';
                  _this.overlays.push(polyline);
                }
                allPoints.push.apply(allPoints, points);
              });

              let view = this.map.getViewport(eval(allPoints));
              let mapZoom = view.zoom;
              let centerPoint = view.center;
              this.map.centerAndZoom(centerPoint, mapZoom);
            }
            timeout += 1;
          }, 500);
        });
      },
      editEditMap() {
        // enableMapClick: false 构造底图时，关闭底图可点功能
        this.editViewMap = new BMap.Map("mapEdit", {enableMapClick: false, mapType: BMAP_HYBRID_MAP});
        this.editViewMap.centerAndZoom(new BMap.Point(108.720027, 34.298497), 15);
        this.editViewMap.enableScrollWheelZoom(true);// 开启鼠标滚动缩放
        this.editViewMap.addControl(new BMap.NavigationControl());
        this.editViewMap.addControl(new BMap.MapTypeControl({
          type: BMAP_MAPTYPE_CONTROL_HORIZONTAL, // 按钮水平方式展示，默认采用此类型展示
          mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP], // 控件展示的地图类型
          anchor: BMAP_ANCHOR_BOTTOM_RIGHT
        }));
        // this.clearEditAll();
        // 加载行政区划
        this.loadEditStaticMapData('xingzheng.geo.json');
        // 加载路网
        this.loadEditStaticMapData('luwang.geo.json');
        console.log('加载鼠标绘制工具...');
        let _this = this;
        return new Promise((resolve, reject) => {
          let drawNode = document.createElement("script");
          drawNode.setAttribute("type", "text/javascript");
          drawNode.setAttribute("src", '/assets/js/DrawingManager_min.js');
          document.body.appendChild(drawNode);
          // 等待页面加载完毕回调
          let timeout = 0;
          let interval = setInterval(() => {
            // 超时10秒加载失败
            if (timeout >= 20) {
              reject();
              clearInterval(interval);
              console.error("鼠标绘制工具加载失败...");
            }
            // 加载成功
            if (typeof BMapLib !== "undefined") {
              resolve(BMapLib);
              clearInterval(interval);
              console.log("鼠标绘制工具加载成功...");
              // let arr = [];
              let overlaycomplete = function (e) {
                e.overlay.drawingMode = e.drawingMode;
                if (e.drawingMode === 'marker') {
                  $('.BMapLib_marker_hover, .BMapLib_marker').css('display', 'none');
                }
                _this.overlays.push(e.overlay);
                _this.Editing('enable');
              };
              let polygonStyleOptions = {
                strokeColor: "#f44336", // 边线颜色。
                fillColor: "#f44336", // 填充颜色。当参数为空时，圆形将没有填充效果。
                fillOpacity: 0.2, // 填充的透明度，取值范围0 - 1。
                strokeWeight: 4, // 边线的宽度，以像素为单位。
                strokeOpacity: 0.8, // 边线透明度，取值范围0 - 1。
                strokeStyle: 'solid' // 边线的样式，solid或dashed。
              };
              let polylineStyleOptions = {
                strokeColor: "#2196f3", // 边线颜色。
                strokeWeight: 4, // 边线的宽度，以像素为单位。
                strokeOpacity: 0.8, // 边线透明度，取值范围0 - 1。
                strokeStyle: 'solid' // 边线的样式，solid或dashed。
              };
              //实例化鼠标绘制工具
              this.drawingManager = new BMapLib.DrawingManager(this.editViewMap, {
                isOpen: false, // 是否开启绘制模式
                enableDrawingTool: true, // 是否显示工具栏
                drawingToolOptions: {
                  anchor: BMAP_ANCHOR_TOP_RIGHT, // 位置
                  offset: new BMap.Size(5, 5), // 偏离值
                  drawingModes: [BMAP_DRAWING_MARKER, BMAP_DRAWING_POLYLINE, BMAP_DRAWING_POLYGON], // 设置只显示画折线和多边形
                },
                polylineOptions: polylineStyleOptions, // 折线样式
                polygonOptions: polygonStyleOptions, // 多边形样式
              });

              // 添加鼠标绘制工具监听事件，用于获取绘制结果
              this.drawingManager.addEventListener('overlaycomplete', overlaycomplete);
              this.drawingManager.addEventListener("markercomplete", function (e, overlay) {
                _this.drawingManager.setDrawingMode('hander');
              });
              let allPoints = [];
              let markerPoints = this.editForm.center_point.coordinates;
              let marker = new BMap.Marker(new BMap.Point(markerPoints.lng, markerPoints.lat), {
                strokeColor: "blue",
                strokeWeight: 3,
                strokeOpacity: 0.5,
                fillColor: ''
              });
              allPoints.push.apply(allPoints, [new BMap.Point(markerPoints.lng, markerPoints.lat)]);
              this.editViewMap.addOverlay(marker);
              marker.drawingMode = 'marker';
              this.overlays.push(marker);
              let _this = this;
              this.editForm.positions.forEach(function (e) {
                let points = [];
                e.coordinates.forEach(function (el) {
                  points.push(new BMap.Point(el.lng, el.lat));
                });
                if (e.drawingMode === 'polygon') {
                  let polygon = new BMap.Polygon(points, {
                    strokeColor: "blue",
                    strokeWeight: 3,
                    strokeOpacity: 0.5,
                    fillColor: ''
                  });
                  _this.editViewMap.addOverlay(polygon);
                  polygon.drawingMode = 'polygon';
                  _this.overlays.push(polygon);
                } else {
                  let polyline = new BMap.Polyline(points, {
                    strokeColor: "blue",
                    strokeWeight: 3,
                    strokeOpacity: 0.5,
                    fillColor: ''
                  });
                  _this.editViewMap.addOverlay(polyline);
                  polyline.drawingMode = 'polyline';
                  _this.overlays.push(polyline);
                }
                allPoints.push.apply(allPoints, points);
              });

              let view = this.editViewMap.getViewport(eval(allPoints));
              let mapZoom = view.zoom;
              let centerPoint = view.center;
              this.editViewMap.centerAndZoom(centerPoint, mapZoom);
            }
            timeout += 1;
          }, 500);
        });
      },
      isEdit(state) {
        this.Editing(state);
        this.drawingManager.close();
      },
      Editing(state) {
        for (let i = 0; i < this.overlays.length; i++) {
          if (this.overlays[i].drawingMode !== 'marker') {
            state === 'enable' ? this.overlays[i].enableEditing() : this.overlays[i].disableEditing();
          } else {
            state === 'enable' ? this.overlays[i].enableDragging() : this.overlays[i].disableDragging();
          }
        }
      },
      searchProject() {
        this.clickSearch = true;
        this.getProject();
      },
      getProject() {
        this.tableLoading = true;
        this.searchForm.is_audit = this.$route.params.is_audit;
        getAllProjects(this.searchForm).then(e => {
          this.tableLoading = false;
          this.data = e.result;
          this.dataCount = e.total;
          this.exportBtnDisable = !this.clickSearch;
        });
      },
      goToAudit(row) {
        toAudit(row.id).then(res => {
          if (res.result) {
            this.$Message.success('提交成功!');
            this.init();
          }
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
        this.$refs.searchForm.resetFields();
        this.dataCount = 0;
        this.searchForm.pageNumber = 1;
        this.searchForm.pageSize = 10;
        this.getProject();
      },
      addProject() {
        this.planDisplay = false;
        this.addMap = true;
        this.showMap = false;
        this.modal = true;
        this.form.projectPlan = '';
        let departmentId = this.$store.getters.user.department_id;
        this.form.unit = this.departmentIds[departmentId];
      },
      handleSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            let err_sum = '';
            let year_count_amount = 0;
            if (this.form.projectPlan.length > 0) {
              let _this = this;
              this.form.projectPlan.forEach(function (row) {
                year_count_amount += row.amount;
                if (row.date === _this.currentYear && row.total_count_amount !== row.amount) {
                  err_sum = row.date;
                }
              });
            }
            if (err_sum !== '') {
              this.$Message.error(err_sum + "年月度累计金额不等于年计划");
            } else {
              if (year_count_amount > this.form.amount) {
                this.$Message.error("年度累计金额不能大于总金额！");
              } else {
                this.loading = true;
                addProject(this.form).then(e => {
                  this.loading = false;
                  if (e.result) {
                    this.$Message.success('添加成功!');
                    this.modal = false;
                    this.$refs['formValidate'].resetFields();
                    this.planDisplay = false;
                    this.form.projectPlan = '';
                    this.getProject();
                  } else {
                    if (e.message === '登录超时，请重新登陆') {
                      this.$Message.error(e.message);
                      this.$router.push({
                        name: 'login'
                      });
                    } else {
                      this.$Message.error('添加失败!');
                    }
                  }
                });
              }
            }
          } else {
            this.$Message.error('请填写必填字段!');
          }
        });
      },
      editSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            let err_sum = '';
            let year_count_amount = 0;
            if (this.editForm.projectPlan.length > 0) {
              let _this = this;
              this.editForm.projectPlan.forEach(function (row) {
                year_count_amount += row.amount;
                if (row.date === _this.currentYear && parseFloat(row.total_count_amount) !== parseFloat(row.amount)) {
                  err_sum = row.date;
                }
              })
            }
            if (err_sum !== '') {
              this.$Message.error(err_sum + "月度累计金额不等于年计划");
            } else if (parseFloat(year_count_amount) > parseFloat(this.form.amount)) {
              this.$Message.error("年度累计金额不能大于总金额！");
            } else {
              this.loading = true;
              edit(this.editForm).then(e => {
                this.loading = false;
                if (e.result) {
                  this.$Message.success('修改成功!');
                  this.editModal = false;
                  this.init();
                } else {
                  if (e.message === '登录超时，请重新登陆') {
                    this.$Message.error(e.message);
                    this.$router.push({
                      name: 'login'
                    });
                  } else {
                    this.$Message.error('修改失败!');
                  }
                }
              });
            }
          } else {
            this.$Message.error('请填写必填字段!');
          }
        })
      }
      ,
      handleReset(name) {
        this.form.projectPlan = '';
        this.planDisplay = false;
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
        this.$refs['formValidate'].resetFields();
      },
      cancelReasonForm() {
        this.$refs.reasonForm.resetFields();
      },
      buildYearPlan() {
        let startDate = this.form.plan_start_at;
        if (startDate) {
          let endDate = this.form.plan_end_at;
          if (endDate >= startDate) {
            buildPlanFields([startDate, endDate]).then(res => {
              if (res.result) {
                res.result.forEach(function (row, index) {
                  let CurrentDate = new Date();
                  let CurrentYear = CurrentDate.getFullYear();
                  // 如果是当年，年度计划和月度计划都为必填
                  if (row.date === CurrentYear) {
                    row.role = {required: true, message: '计划投资金额不能为空', trigger: 'blur', type: 'number'};
                    row.imageProgress = {required: true, message: '计划形象进度不能为空', trigger: 'blur'};
                    row.placeholder = '必填项';
                    row.required = true;
                    row.month.forEach(function (e) {
                      e.monthRole = {required: true, message: '月计划投资金额不能为空', trigger: 'blur', type: 'number'};
                      e.monthImageProgress = {required: true, message: '月计划形象进度不能为空', trigger: 'blur'};
                      e.monthPlaceholder = '必填项';
                    });
                    // 如果是之前年，年度计划为必填，月度计划非必填
                  } else if (row.date < CurrentYear) {
                    row.role = {required: true, message: '计划投资金额不能为空', trigger: 'blur', type: 'number'};
                    row.imageProgress = {required: true, message: '计划形象进度不能为空', trigger: 'blur'};
                    row.placeholder = '必填项';
                    row.required = false;
                    row.month.forEach(function (e) {
                      e.monthRole = {required: false, type: 'number'};
                      e.monthImageProgress = {required: false};
                      e.monthPlaceholder = '非必填';
                    });
                  } else {
                    row.role = {required: false, type: 'number'};
                    row.imageProgress = {required: false};
                    row.placeholder = '非必填';
                    row.required = false;
                    row.month.forEach(function (e) {
                      e.monthRole = {required: false, type: 'number'};
                      e.monthImageProgress = {required: false};
                      e.monthPlaceholder = '非必填';
                    });
                  }
                });
                this.planDisplay = true;
                this.form.projectPlan = res.result;
              }
            });
          } else {
            this.form.plan_end_at = null;
            this.$Message.error('结束时间应 >= 开始时间!');
          }
        } else {
          this.form.plan_end_at = null;
          this.$Message.error('请先选择开始时间!');
        }
      },
      buildEditYearPlan() {
        let startDate = this.editForm.plan_start_at;
        if (startDate) {
          let endDate = this.editForm.plan_end_at;
          if (endDate >= startDate) {
            buildPlanFields([startDate, endDate]).then(res => {
              if (res.result) {
                let projectPlans = this.editForm.projectPlan;
                res.result.forEach(function (row, index) {
                  let CurrentDate = new Date();
                  let CurrentYear = CurrentDate.getFullYear();
                  // 如果是当年，年度计划和月度计划都为必填

                  projectPlans.forEach(function (rowP, indexP) {
                    if (rowP.date === row.date) {
                      row.amount = rowP.amount;
                      row.image_progress = rowP.image_progress;
                      row.month.forEach(function (e) {
                        rowP.month.forEach(function (p) {
                          if (e.date === p.date) {
                            e.amount = p.amount;
                            e.image_progress = p.image_progress;
                          }
                        });
                      });
                    }
                  });
                  if (row.date === CurrentYear) {
                    row.role = {required: true, message: '计划投资金额不能为空', trigger: 'blur', type: 'number'};
                    row.imageProgress = {required: true, message: '计划形象进度不能为空', trigger: 'blur'};
                    row.placeholder = '必填项';
                    row.required = true;
                    if (row.month !== undefined) {
                      row.month.forEach(function (e) {
                        e.monthRole = {required: true, message: '月计划投资金额不能为空', trigger: 'blur', type: 'number'};
                        e.monthImageProgress = {required: true, message: '月计划形象进度不能为空', trigger: 'blur'};
                        e.monthPlaceholder = '必填项';
                      });
                    }
                    // 如果是之前年，年度计划为必填，月度计划非必填
                  } else if (row.date < CurrentYear) {
                    row.role = {required: true, message: '计划投资金额不能为空', trigger: 'blur', type: 'number'};
                    row.imageProgress = {required: true, message: '计划形象进度不能为空', trigger: 'blur'};
                    row.placeholder = '必填项';
                    row.required = false;
                    if (row.month !== undefined) {
                      row.month.forEach(function (e) {
                        e.monthRole = {required: false, type: 'number'};
                        e.monthImageProgress = {required: false};
                        e.monthPlaceholder = '非必填';
                      });
                    }
                  } else {
                    row.role = {required: false, type: 'number'};
                    row.imageProgress = {required: false};
                    row.placeholder = '非必填';
                    row.required = false;
                    if (row.month !== undefined) {
                      row.month.forEach(function (e) {
                        e.monthRole = {required: false, type: 'number'};
                        e.monthImageProgress = {required: false};
                        e.monthPlaceholder = '非必填';
                      });
                    }
                  }
                });
                this.editForm.projectPlan = res.result;
              }
            });
          } else {
            this.editForm.plan_end_at = null;
            this.$Message.error('结束时间应 >= 开始时间!');
          }
        } else {
          this.editForm.plan_end_at = null;
          this.$Message.error('请先选择开始时间!');
        }
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
        auditProject(params).then(res => {
          if (res.result === true) {
            if (params.status === 1) {
              this.$Message.success('审核通过!');
            } else {
              this.reasonAuditLoading = false;
              this.reasonModal = false;
              this.$Message.error('审核不通过!');
            }
            this.previewModal = false;
            this.getProject();
          }
        });
      },
      onAddIsGcChange(value) {
        if (value !== 1) {
          this.form.nep_type = '';
          this.isGcRole = {required: false};
          this.$refs.formValidate.$children[6].$el.children[1].children[0].className = 'ivu-form-item';
        } else {
          this.isGcRole = {required: true, message: '国民经济计划分类不能为空', trigger: 'change', type: 'number'};
          this.$refs.formValidate.$children[6].$el.children[1].children[0].className = 'ivu-form-item ivu-form-item-required';
        }
        this.addNepDisabled = value !== 1;
      },
      onEditIsGcChange(value) {
        if (value !== 1) {
          this.editForm.nep_type = '';
          this.isGcRole = {required: false};
          this.$refs.formValidate.$children[6].$el.children[1].children[0].className = 'ivu-form-item';
        } else {
          this.isGcRole = {required: true, message: '国民经济计划分类不能为空', trigger: 'change', type: 'number'};
          this.$refs.formValidate.$children[6].$el.children[1].children[0].className = 'ivu-form-item ivu-form-item-required';
        }
        this.addNepDisabled = value !== 1;
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
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getProject();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getProject();
      },
      //月投资计划金额   大于计划金额时不能填写
      totalAmount(index_t, index) {
        let amount_total = this.form.projectPlan[index_t].amount;
        let month_total = this.form.projectPlan[index_t].month;
        if (!amount_total) {
          this.$Message.error('请先填写年计划金额!');
          month_total[index].amount = 0;
          return;
        }
        month_total.length;
        let amounts = 0;
        for (let i = 0; i < month_total.length; i++) {
          if (month_total[i].amount) {
            amounts = parseFloat(amounts) + parseFloat(month_total[i].amount);
          }
        }
        this.form.projectPlan[index_t].total_count_amount = amounts;
        if (amounts > amount_total) {
          this.$Message.error('月计划总金额不能超过年计划');
          this.form.projectPlan[index_t].total_count_amount = amounts - month_total[index].amount;
          month_total[index].amount = 0;
        }
      },
      //修改月投资计划金额   大于计划金额时不能填写
      totalAmountE(index_t, index) {
        let amount_total = this.editForm.projectPlan[index_t].amount;
        let month_total = this.editForm.projectPlan[index_t].month;
        if (!amount_total) {
          this.$Message.error('请先填写年计划金额!');
          month_total[index].amount = 0;
          return;
        }
        let amounts = 0;
        for (let i = 0; i < month_total.length; i++) {
          if (month_total[i].amount) {
            amounts = parseFloat(amounts) + parseFloat(month_total[i].amount);
          }
        }
        this.editForm.projectPlan[index_t].total_count_amount = amounts;
        if (amounts > amount_total) {
          this.$Message.error('月计划总金额不能超过年计划');
          this.editForm.projectPlan[index_t].total_count_amount = amounts - month_total[index].amount;
          month_total[index].amount = 0;
        }
      },
      //导出
      exportSchedule() {
        let title = this.searchForm.title;
        let subject = this.searchForm.subject;
        let office = this.searchForm.office;
        let unit = this.searchForm.unit;
        let num = this.searchForm.num;
        let type = this.searchForm.type;
        let build_type = this.searchForm.build_type;
        let money_from = this.searchForm.money_from;
        let is_gc = this.searchForm.is_gc;
        let nep_type = this.searchForm.nep_type;
        let status = this.searchForm.status;
        window.location.href = "/api/project/exportProject?title=" + title + "&subject=" + subject + "&office=" + office + "&unit=" + unit + "&num=" + num + "&type=" + type + "&build_type=" + build_type + "&money_from=" + money_from + "&is_gc=" + is_gc + "&nep_type=" + nep_type + "&status=" + status;
      },
      // 发起项目调整
      projectAdjustment() {
        this.projectModal = true;
      },
      // 调整项目
      projectAdjustmentOk() {
        this.projectModal = false;
        this.$Modal.confirm({
          title: "本次调整将不可撤销，确认是否需要调整",
          loading: true,
          okText: "取消",
          cancelText: "确定",
          content: '',
          onOk: () => {
            this.$Modal.remove();
          },
          onCancel: () => {
            let project_ids = [];
            this.projectAdjustmentIds.forEach(function (el) {
              project_ids.push(el.id);
            });
            projectAdjustment({project_ids: project_ids}).then(e => {
              if (e.result) {
                this.$Message.success("调整成功");
                this.getProject();
              } else {
                this.$Message.error('调整失败!');
              }
              this.$Modal.remove();
              this.loading = false;
            });
          }
        })
      },
      //调整项目  取消
      projectAdjustmentCancel() {
        this.$Modal.remove()
      },
      //选中
      checkboxProject(selection) {
        // console.log(selection.length);
        this.projectBtnDisable = selection.length <= 0;
        this.projectAdjustmentIds = selection;
      },
      clearComma(s) {
        if ($.trim(s) === "") {
          return s;
        } else {
          return (s + "").replace(/[,]/g, "");
        }
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
      this.mapStyle.height = window.innerHeight - 112 + 'px';
      this.mapStyle.width = window.innerWidth + 'px';
      this.init();
    }
  }
</script>
