<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="70" class="search-form">
        <Form-item label="项目名称" prop="title">
          <Input clearable v-model="searchForm.title" placeholder="支持模糊搜索" style="width: 200px"/>
        </Form-item>
        <Form-item label="项目编号" prop="num">
          <Input clearable v-model="searchForm.num" placeholder="请输入项目编号" style="width: 200px"/>
        </Form-item>
        <span v-if="drop">
          <Form-item label="投资主体" prop="subject">
            <Input clearable v-model="searchForm.subject" placeholder="支持模糊搜索" style="width: 200px"/>
          </Form-item>
          <Form-item label="承建单位" prop="unit">
            <Input clearable v-model="searchForm.unit" placeholder="支持模糊搜索" style="width: 200px"/>
          </Form-item>
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
      <Button type="primary" @click="addProject" icon="md-add" v-if="isShowButton">添加项目</Button>
      <Button class="exportReport" @click="exportSchedule" v-if="showExportButton" type="primary"
              :disabled="exportBtnDisable" icon="md-cloud-upload">
        导出项目
      </Button>
    </p>
    <Row>
      <Table type="selection" stripe border :columns="columns" :data="nowData" :loading="tableLoading"></Table>
    </Row>
    <Row type="flex" justify="end" class="page">
      <Page :total="dataCount" :page-size="pageSize" @on-change="changePage" @on-page-size-change="_nowPageSize"
            show-total show-sizer :current="pageCurrent"/>
    </Row>
    <Modal
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="创建项目">
      <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="130">
        <Divider><h4>基础信息</h4></Divider>
        <Row>
          <Col span="12">
            <FormItem label="项目名称" prop="title">
              <Input v-model="form.title" placeholder="必填项"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目编号" prop="num">
              <Input v-model="form.num" placeholder="非必填"></Input>
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
              <Input v-model="form.unit" placeholder="子公司或部门"></Input>
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
            <FormItem label="项目地理位置">
              <Row>
                <Col span="12">
                  <Button type="info" long :disabled="addDisabled" @click="chooseArea" icon="md-add">绘制地图</Button>
                </Col>
              </Row>
              <Row>
                <Col span="12">
                  <Button type="primary" :disabled="editDisabled" long @click="" icon="ios-create">修改</Button>
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
          <Col span="12">
            <FormItem label="项目概况" prop="description">
              <Input v-model="form.description" type="textarea" :rows="4" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Divider v-if="planDisplay"><h4>项目投资计划</h4></Divider>
        <div v-for="(item, index_t) in form.projectPlan">
          <Divider orientation="left"><h5 style="color: #2d8cf0;">{{item.date}}年项目投资计划</h5></Divider>
          <Row>
            <Col span="12">
              <FormItem
                label="计划投资金额（万元）"
                :prop="'projectPlan.' + index_t + '.amount'"
                :rules="item.role">
                <InputNumber :min="0" :step="1.2" v-model="item.amount" :placeholder="item.placeholder"></InputNumber>
              </FormItem>
            </Col>
            <Col span="12">
              <FormItem
                label="计划形象进度"
                :prop="'projectPlan.' + index_t + '.image_progress'">
                <Input v-model="item.image_progress" type="textarea" :rows="1" placeholder="请输入..."></Input>
              </FormItem>
            </Col>
          </Row>
          <Row>
            <Collapse class="collapse">
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
                      <InputNumber :min="0" :step="1.2" v-model="ite.amount" @on-blur="totalAmount(index_t,index)"
                                   placeholder=""
                                   class="monthInput"></InputNumber>
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
        <!--        <Input id="suggestId" search placeholder="Enter something..." />-->
        <Button type="error" @click="clearAll()" style="margin-left: 8px">清除重绘</Button>
        <Button type="success" @click="complete()">完成</Button>
      </div>
    </Modal>
    <Modal
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
      <Form ref="editFormValidate" :model="editForm" :rules="ruleValidate" :label-width="110">
        <Divider><h4>基础信息</h4></Divider>
        <Row>
          <Col span="12">
            <FormItem label="项目名称" prop="title">
              <Input v-model="editForm.title" placeholder="必填项" v-bind:disabled="isAdjustReadOnly"/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目编号" prop="num">
              <Input v-model="editForm.num" placeholder="非必填" v-bind:disabled="isAdjustReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目类型" prop="type">
              <Select v-model="editForm.type" v-bind:disabled="isAdjustReadOnly">
                <Option v-for="item in dict.type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="editForm.subject" placeholder="必填项" v-bind:disabled="isAdjustReadOnly"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="建设性质" prop="build_type">
              <Select v-model="editForm.build_type" v-bind:disabled="isAdjustReadOnly">
                <Option v-for="item in dict.build_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="承建单位" prop="unit">
              <Input v-model="editForm.unit" placeholder="子公司或部门" v-bind:disabled="isAdjustReadOnly"></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="资金来源" prop="money_from">
              <Select v-model="editForm.money_from" v-bind:disabled="isAdjustReadOnly">
                <Option v-for="item in dict.money_from" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目金额(万元)" prop="amount">
              <InputNumber :min="0" :step="1.2" v-model="editForm.amount" placeholder=""
                           v-bind:disabled="isAdjustReadOnly">
              </InputNumber>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12" v-if="showLandMoney">
            <FormItem label="土地费用(万元)" prop="land_amount">
              <InputNumber :step="1.2" :min="0" v-model="editForm.land_amount" placeholder=""
                           v-bind:disabled="isAdjustReadOnly">
              </InputNumber>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="项目状态" prop="status">
              <Select v-model="editForm.status" v-bind:disabled="isAdjustReadOnly">
                <Option v-for="item in dict.status" :value="item.value" :key="item.value">{{item.title}}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="项目标识(是否为国民经济计划)" prop="is_gc">
              <Select v-model="editForm.is_gc" @on-change="onAddIsGcChange" v-bind:disabled="isAdjustReadOnly">
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
                          v-model="editForm.plan_start_at" v-bind:disabled="isAdjustReadOnly"></DatePicker>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="计划结束时间" prop="plan_end_at">
              <DatePicker type="month" @on-change="buildYearPlan" placeholder="结束时间" format="yyyy年MM月"
                          v-model="editForm.plan_end_at" v-bind:disabled="isAdjustReadOnly"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <FormItem label="项目中心点坐标" prop="center_point">
            <Input v-model="editForm.center_point" placeholder="" v-bind:disabled="isAdjustReadOnly"/>
          </FormItem>
          <FormItem label="项目轮廓点坐标" prop="positions">
            <Input v-model="editForm.positions" placeholder="" disabled/>
          </FormItem>
        </Row>
        <Row>
          <FormItem label="项目概况" prop="description">
            <Input v-model="editForm.description" type="textarea" :rows="4" placeholder="请输入..."
                   v-bind:disabled="isAdjustReadOnly"></Input>
          </FormItem>
        </Row>
        <Divider><h4>投资计划</h4></Divider>
        <div v-for="(item, index_t) in editForm.projectPlan">
          <Divider orientation="left"><h5 style="color: #2d8cf0;">{{item.date}}年项目投资计划</h5></Divider>
          <Row>
            <Col span="12">
              <FormItem
                label="计划投资金额（万元）"
                :prop="'projectPlan.' + index_t + '.amount'"
                :rules="{required: true, message: '计划投资金额不能为空', trigger: 'blur', type: 'number'}">
                <InputNumber :min="1" :step="1.2" v-model="item.amount" placeholder="" v-bind:disabled="isReadOnly">
                </InputNumber>
              </FormItem>
            </Col>
            <Col span="12">
              <FormItem
                label="计划形象进度"
                :rules="{required: true, message: '计划形象进度不能为空', trigger: 'blur'}"
                :prop="'projectPlan.' + index_t + '.image_progress'">
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
                <InputNumber :min="0" :step="1.2" v-model="ite.amount" @on-blur="totalAmountE(index_t,index)"
                             placeholder="" class="monthInput"></InputNumber>
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
      <Alert type="error" show-icon v-if="openErrorAlert">
        审核意见
        <span slot="desc">
              {{previewForm.reason}}
          </span>
      </Alert>
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
          <Col span="12" v-if="showLandMoney">
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
  </Card>
</template>
<script>
  import {
    initProjectInfo,
    edit,
    getEditFormData,
    getAllProjects,
    addProject,
    getProjectDictData,
    buildPlanFields,
    auditProject,
    toAudit,
    projectDelete
  } from '../../../api/project';
  import './projects.css'
  import '../../../../../../public/assets/css/DrawingManager_min.css';
  import $ from 'jquery'

  export default {
    data: function () {
      return {
        editDisabled: true,
        addDisabled: false,
        iframeHeight: 0,
        modal11: false,
        pageSize: 10,   // 每页显示多少条
        dataCount: 0,   // 总条数
        pageCurrent: 1, // 当前页
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
        editFormLoading: false,
        planDisplay: false,
        reasonForm: {
          reason: ''
        },
        openErrorAlert: false,
        searchForm: {
          title: '',
          subject: '',
          office: '',
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
            type: 'index2',
            width: 60,
            align: 'center',
            fixed: 'left',
            render: (h, params) => {
              return h('span', params.index + (this.pageCurrent - 1) * this.pageSize + 1);
            }
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
            width: 200,
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
                      this.showAuditButton = this.office === 1 ? params.row.is_audit === 0 : false;
                      this.previewForm = params.row;
                      this.formId = params.row.id;
                      this.isReadOnly = true;
                      this.openErrorAlert = (this.previewForm.reason !== '' && this.previewForm.is_audit === 2);
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
                        if (params.row.is_audit === 3) {
                          this.addNepDisabled = params.row.is_audit === 3;
                        } else {
                          this.addNepDisabled = this.editForm.is_gc !== 1;
                          if (this.addNepDisabled) {
                            this.editForm.nep_type = '';
                          }
                        }
                        this.formId = params.row.id;
                        this.isAdjustReadOnly = params.row.is_audit === 3;
                        this.isReadOnly = false;
                        this.openErrorAlert = (this.editForm.reason !== '' && this.editForm.is_audit === 2);
                        this.editModal = true;
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
          positions: {},
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
          unit: [
            {required: true, message: '建设单位不能为空', trigger: 'blur'}
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
        mapStyle: {
          height: '',
          width: ''
        }
      }
    },
    methods: {
      init() {
        this.office = this.$store.getters.user.office;
        if (this.office === 2) {
          this.showLandMoney = true;
        }
        this.isShowButton = this.office === 0;
        this.showExportButton = !(this.office === 0);
        this.$refs.formValidate.resetFields();
        this.iframeHeight = this.$parent.$el.clientHeight - 160;
        initProjectInfo().then(res => {
          if (res.result) {
            this.project_list = res.result;
          }
        });
        this.getDictData();
        this.getProject();
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
        let polygon = '108.62517316456,34.265751768515;108.62690180941,34.26345772659;108.62932106806,34.262003356365;108.63139595822,34.260269096061;108.63416217973,34.258806680023;108.63623729029,34.257922481029;108.63935077934,34.255602953814;108.64211786001,34.254141257635;108.64453822074,34.25353839176;108.64764946502,34.252645092428;108.65041349677,34.252611860492;108.65076106534,34.249485372282;108.64834268388,34.249513806553;108.64799830736,34.24724747527;108.64799905942,34.245827899473;108.64765381817,34.244696885783;108.64627193409,34.243295720108;108.64454412874,34.241616156643;108.64281571422,34.240505612941;108.64039574417,34.239122263857;108.63970437976,34.23742949682;108.63832150392,34.236315360593;108.63659243565,34.236342763933;108.6362468463,34.234644512193;108.63728355189,34.232924380725;108.63866630684,34.231766472154;108.63797432963,34.230357417794;108.63762757057,34.228374947544;108.63762684087,34.226670887199;108.63589803674,34.226698317832;108.63416979038,34.227293773244;108.63416882177,34.225305349085;108.63416740587,34.223317145835;108.63451210034,34.22160740555;108.63623890849,34.219591463655;108.63727451302,34.217586336327;108.63900253955,34.216991101956;108.63934634678,34.21499690541;108.63934399652,34.212724120519;108.63934237525,34.211019508604;108.6393390361,34.207894090569;108.63104180505,34.207456594946;108.6296538402,34.202078957574;108.62861569902,34.200389297902;108.63034173299,34.198942769805;108.63656312192,34.198561088297;108.6365615777,34.196856229548;108.63586871504,34.195446408048;108.6393269944,34.196243980417;108.6431280789,34.194197444789;108.64208913684,34.191939572056;108.64277844297,34.188803531453;108.6431226014,34.18709358258;108.64415906088,34.185942012792;108.6469243648,34.186188074796;108.6493434409,34.186441925451;108.65176119324,34.185562734632;108.65452372355,34.186388926275;108.65659465283,34.186373248031;108.65866385122,34.186076536581;108.66038781894,34.185500522634;108.66142039971,34.182655355277;108.66107471532,34.179246853772;108.66107410282,34.175836920853;108.66107414616,34.172143448827;108.67380608541,34.171891826153;108.67552340262,34.17645347777;108.67689783336,34.180161288101;108.68032962947,34.181338838573;108.68307327782,34.182230132797;108.68650079085,34.182852923055;108.69026941735,34.183487385045;108.69335140782,34.184112697;108.69061365251,34.187187635169;108.68787614022,34.190548306107;108.68650695068,34.192230154463;108.68513809525,34.194196434898;108.68685311248,34.196213885652;108.69096407854,34.196855065301;108.69370331236,34.196621706164;108.69746816236,34.195272483527;108.70123696054,34.197900755087;108.70740181775,34.198007849251;108.70876969269,34.195187785724;108.71013784825,34.192366771909;108.71322297701,34.191556246873;108.71802608064,34.191607184243;108.72455339873,34.191361384916;108.72523796374,34.186248480524;108.73005480102,34.185964705559;108.73005366578,34.183975772376;108.73246359577,34.181410344898;108.73487498541,34.179692742827;108.73763319257,34.17967228625;108.73970292029,34.180221158891;108.73970326175,34.181641622901;108.74660632109,34.180988418916;108.747987304,34.179263638723;108.74936908142,34.180379296589;108.74936998513,34.182652168952;108.74971656501,34.184920172822;108.7514440966,34.186029531627;108.7528269825,34.187712126843;108.75386456766,34.189399721224;108.75248373885,34.190843501683;108.75179470117,34.193127872495;108.75006977234,34.195428702336;108.75041720679,34.197696444055;108.75076503938,34.199964184649;108.75076777981,34.202521380432;108.74973424843,34.205095010374;108.74801008022,34.207678474023;108.74628485176,34.209123841632;108.7449057837,34.211131813574;108.74352669859,34.212854707898;108.74352904922,34.215127494998;108.74491150924,34.21681404896;108.74663948739,34.218494532368;108.74767719504,34.220752195241;108.74664284005,34.222755753879;108.74457219983,34.22420442806;108.74250174771,34.225366527563;108.74077724769,34.227090498789;108.74181337151,34.228498958893;108.74181405306,34.230486750989;108.74043428834,34.232206223013;108.74008967995,34.234197513196;108.74043497708,34.236181777682;108.74043514851,34.238169320542;108.73870995594,34.240174070978;108.73870971956,34.242161485887;108.73939858568,34.244993845648;108.73974302665,34.246977657274;108.74043178876,34.249241410619;108.74008573737,34.250947958416;108.73904945326,34.253229386269;108.73766819487,34.255228562222;108.73766674642,34.257215324111;108.73835496779,34.258911972775;108.73800914055,34.260333939526;108.73731722896,34.262326348933;108.73835057086,34.264020278083;108.74007476604,34.264571002531;108.74318043784,34.264819394156;108.7452514853,34.265360092951;108.74628527811,34.26761576922;108.74801051611,34.269009752224;108.74973585081,34.270685726578;108.75215194952,34.272633887988;108.75353216612,34.274597289623;108.75560385258,34.275981951343;108.7556019584,34.278251862104;108.7556007049,34.280238138488;108.75559959122,34.282224458888;108.7559434695,34.284488664788;108.75697893452,34.286173790477;108.75870544551,34.287563321142;108.76043131121,34.290088597454;108.76077659168,34.292069189885;108.76008585679,34.293783336775;108.7587041953,34.29522523287;108.75697760528,34.297240443928;108.75559646993,34.299250200945;108.75421494133,34.300975641198;108.75387066268,34.303251915307;108.75456269001,34.305510523283;108.75560007812,34.307479780797;108.75698274329,34.309159107965;108.75663901166,34.311435425294;108.75664078758,34.313421842967;108.7559518666,34.315420036842;108.7566449408,34.317962178906;108.7559567805,34.320528104411;108.75354157985,34.323122124581;108.75147048426,34.324574922822;108.75147302963,34.327128623895;108.75251204442,34.329382168742;108.75355025793,34.33135140868;108.75493452526,34.333882023261;108.75631848532,34.33584507716;108.75770180433,34.337807819576;108.75942979909,34.339197736852;108.76150198075,34.339447100425;108.76426364575,34.339402485275;108.76667902384,34.338798195662;108.76978249395,34.338186804955;108.77219513345,34.337872265862;108.77460654023,34.337845037794;108.77632910753,34.338962806517;108.77805145287,34.34121779081;108.77908518862,34.34347939505;108.78011816062,34.34574153757;108.78046308908,34.347725046778;108.78183868661,34.349419192963;108.78252659725,34.351118030073;108.7835573793,34.352531941966;108.78458807852,34.354513990095;108.78493169396,34.356781757239;108.78458814958,34.358767666554;108.78218401235,34.359627129594;108.78046563777,34.358502133053;108.7780579886,34.357668513786;108.77633738878,34.356265315477;108.77530439669,34.354857358828;108.77392673418,34.353170240889;108.77082492632,34.351789077356;108.76668662276,34.35099586858;108.76323485848,34.350198401268;108.76047199556,34.348257886681;108.75770809937,34.346602215237;108.75425186501,34.343256083456;108.75045036825,34.341048052674;108.74595880121,34.33884558554;108.74250530238,34.33747162978;108.73939851198,34.336371082248;108.73456948202,34.334990951807;108.73077959797,34.334156436426;108.72733577431,34.33274187535;108.72183267295,34.331023446547;108.71839707433,34.329864509381;108.71462077748,34.328408188283;108.71119039399,34.327229532024;108.70741964322,34.326037785482;108.70365054327,34.324838821548;108.70056693641,34.323080108957;108.69748338221,34.321035749882;108.69440028529,34.31927452354;108.69200182849,34.317526989366;108.68960305174,34.315780419899;108.6872032702,34.313751892442;108.68514534645,34.311447400497;108.68342938577,34.308582828085;108.68137029568,34.305714802934;108.68033986656,34.302863335092;108.6789663799,34.299440227596;108.67862289291,34.29631459155;108.67827934618,34.29318919608;108.67862300298,34.290639341031;108.67759400593,34.288073708095;108.67622160253,34.285788971262;108.67450593042,34.283218871066;108.67107017332,34.282058339139;108.6669437624,34.280055810298;108.66487994774,34.277783313124;108.66212486218,34.276650768238;108.65867813166,34.275812470813;108.65591911826,34.274694858006;108.65315832692,34.273583260154;108.65005138266,34.272196780189;108.64625158913,34.27082546746;108.64245013342,34.269744829283;108.64003092676,34.26864648809;108.63588232766,34.268144377259;108.63242653334,34.267063839835;108.62931654772,34.266827688505;108.62655366893,34.266300225671;108.62517316456,34.265751768515;';
        let ply = new BMap.Polygon(polygon, {strokeWeight: 3}); //建立多边形覆盖物
        ply.setFillOpacity(0.01);
        this.map.addOverlay(ply);  //添加覆盖物

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
      chooseArea() {
        $("#map").empty();
        this.modal11 = true;
        console.log("初始化百度地图脚本...");
        const AK = "rdxXZeTCdtOAVL3DlNzYkXas9nR21KNu";
        const apiVersion = "3.0";
        const timestamp = new Date().getTime();
        const BMap_URL = "http://api.map.baidu.com/getscript?v=" + apiVersion + "&ak=" + AK + "&services=&t=" + timestamp;
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
      clearAll() {
        for (let i = 0; i < this.overlays.length; i++) {
          this.map.removeOverlay(this.overlays[i]);
        }
        this.overlays.length = 0;
        $('.BMapLib_marker_hover, .BMapLib_marker').css('display', 'inherit');
      },
      complete() {
        let length = this.overlays.length;
        if (length === 0) {
          this.$Message.error('未选择任何区域或标点!');
        } else if (length === 1) {
          if (this.overlays[0].drawingMode !== 'marker') {
            this.$Message.error('请插入标注点!');
          } else {
            this.$Message.error('请选择项目区域!');
          }
        } else {
          for (let i = 0; i < length; i++) {
            let overlay;
            if (this.overlays[i].drawingMode === 'marker') {
              overlay = this.overlays[i].point;
              this.form.center_point = {
                "drawingMode": this.overlays[i].drawingMode,
                "geo": overlay
              };
            } else {
              overlay = this.overlays[i].getPath();
              this.form.positions = {
                "drawingMode": this.overlays[i].drawingMode,
                "geo": overlay
              };
              this.modal11 = false;
              this.$Message.success('地图绘制成功!');
              this.editDisabled = false;
              this.addDisabled = true;
              this.map = {};
              this.drawingManager = {};
            }
          }
        }
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
      getProject() {
        this.tableLoading = true;
        this.searchForm.is_audit = this.$route.params.is_audit;
        getAllProjects(this.searchForm).then(e => {
          this.data = e.result;
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

          if (this.searchForm.title || this.searchForm.subject || this.searchForm.office || this.searchForm.unit || this.searchForm.num || this.searchForm.type || this.searchForm.build_type || this.searchForm.money_from || this.searchForm.is_gc || this.searchForm.nep_type || this.searchForm.status) {
            this.exportBtnDisable = false;
          }
          this.tableLoading = false;
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
        this.searchForm = {
          title: '',
          subject: '',
          office: '',
          unit: '',
          num: '',
          type: '',
          build_type: '',
          money_from: '',
          is_gc: '',
          nep_type: '',
          status: '',
          is_audit: ''
        };
        this.pageCurrent = 1;
        this.getProject();
      },
      addProject() {
        this.planDisplay = false;
        this.modal = true;
        this.form.projectPlan = '';
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
                this.$refs['formValidate'].resetFields();
                this.planDisplay = false;
                this.form.projectPlan = '';
                this.getProject();
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
                  if (row.date === CurrentYear) {
                    row.role = {required: true, message: '计划投资金额不能为空', trigger: 'blur', type: 'number'};
                    row.placeholder = '必填项';
                  } else {
                    row.role = {required: false, type: 'number'};
                    row.placeholder = '非必填';
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
      },//月投资计划金额   大于计划金额时不能填写
      totalAmount(index_t, index) {
        let amount_total = this.form.projectPlan[index_t].amount;
        let month_total = this.form.projectPlan[index_t].month;
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
        if (amounts > amount_total) {
          this.$Message.error('月计划总金额不能超过年计划');
          month_total[index].amount = 0;
        }
      },//修改月投资计划金额   大于计划金额时不能填写
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
        if (amounts > amount_total) {
          this.$Message.error('月计划总金额不能超过年计划');
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
      }
    },
    mounted() {
      this.mapStyle.height = window.innerHeight - 112 + 'px';
      this.mapStyle.width = window.innerWidth + 'px';
      this.init();
    }
  }
</script>
