<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="70" class="search-form">
        <Form-item label="项目名称" prop="project_id">
          <Select v-model="searchForm.project_id"
                  style="width: 200px">
            <Option v-for="item in project_id" :value="item.id" :key="item.id">{{ item.title }}</Option>
          </Select>
        </Form-item>
        <Form-item style="margin-left:-35px;" class="br">
          <Button @click="getProjectScheduleList" type="primary" icon="ios-search">搜索</Button>
          <!-- <Button @click="">重置</Button> -->
        </Form-item>
      </Form>
    </Row>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">填报</Button>
      <!-- <Button type="error" disabled icon="md-trash">删除</Button> -->
    </p>
    <Table type="selection" stripe border :columns="columns" :data="data" :loading="tableLoading"></Table>
    <Modal
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="创建项目">
      <Form ref="form" :model="form" :label-width="150" :rules="formValidate">
        <Row>
          <Col span="11">
            <FormItem label="填报月份" prop="month">
              <DatePicker @on-change="changeMonth" type="month" placeholder="填报月份" format="yyyy-MM"
                          v-model="form.month"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="项目名称" prop="project_id">
              <Select v-model="form.project_id" @on-change="changeProject">
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
            <FormItem label="投资主体" prop="subject">
              <Input v-model="form.subject" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="建设起止年限" prop="build_at">
              <Row>
                <Col span="11">
                  <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                              v-model="form.build_start_at"></DatePicker>
                </Col>
                <Col span="2" style="text-align: center">-</Col>
                <Col span="11">
                  <DatePicker type="month" placeholder="结束时间" format="yyyy-MM"
                              v-model="form.build_end_at"></DatePicker>
                </Col>
              </Row>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="总投资" prop="total_investors">
              <Input v-model="form.total_investors" placeholder="必填项"></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="year_investors" prop="plan_investors">
              <Input v-model="form.plan_investors" placeholder="必填项"/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem :label="year_img" prop="plan_img_progress">
              <Input v-model="form.plan_img_progress" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="start_month_img" prop="start_month_img_progress">
              <Input v-model="form.start_month_img_progress" placeholder=""/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem :label="start_month_act" prop="start_act_complete">
              <Input v-model="form.start_act_complete" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="自开始累积完成投资" prop="acc_complete">
              <Input v-model="form.acc_complete" placeholder=""/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem :label="month_img" prop="month_img_progress">
              <Input v-model="form.month_img_progress" placeholder=""/>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="month_act" prop="month_act_complete">
              <Input v-model="form.month_act_complete" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="form.problem" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="开工时间" prop="start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                          v-model="form.start_at"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="计划开工时间" prop="plan_start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                          v-model="form.plan_start_at"></DatePicker>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
              <Input v-model="form.exp_preforma" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="备注" prop="marker">
              <Input v-model="form.marker" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..."></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="形象进度照片" prop="img_progress_pic">
              <Upload
                ref="upload"
                name="img_pic"
                :on-success="handleSuccess"
                multiple
                :format="['jpg','jpeg','png']"
                :max-size="2048"
                action="/api/project/uploadPic">
                <Button icon="ios-cloud-upload-outline">上传</Button>
              </Upload>
            </FormItem>
          </Col>
        </Row>
      </Form>
      <div slot="footer">
        
        <Button
          @click="submitF('form')"
          :loading="submitLoading"
          type="primary"
          icon="ios-create-outline"
          style="margin-left:8px"
        >保存
        </Button>
        <Button @click="handleReset('form')" :loading="loading">重置</Button>
      </div>
    </Modal>
    <!-- 查看model------------------------------------------------------------------------------ -->
    <Modal
      v-model="seeModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="查看页面">
      <Form ref="form" :model="seeForm" :label-width="150">
        <Row>
          <Col span="11">
            <FormItem label="填报月份" prop="month">
              <DatePicker @on-change="changeMonth" type="month" placeholder="填报月份" format="yyyy-MM"
                          v-model="seeForm.month" readonly></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="项目名称" prop="project_id">
              <Input v-model="seeForm.project_id" placeholder="必填项" readonly></Input>
              <!-- <Select v-model="seeForm.project_id"  @on-change="changeProject" readonly>
                <Option v-for="item in project_id" :value="item.id" :key="item.id" disabled>{{ item.title }}</Option>
              </Select> -->
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="项目编号" prop="project_num">
              <Input v-model="seeForm.project_num" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="seeForm.subject" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="建设起止年限" prop="build_at">
              <Row>
                <Col span="11">
                  <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                              v-model="seeForm.build_start_at" readonly></DatePicker>
                </Col>
                <Col span="2" style="text-align: center">-</Col>
                <Col span="11">
                  <DatePicker type="month" placeholder="结束时间" format="yyyy-MM"
                              v-model="seeForm.build_end_at" readonly></DatePicker>
                </Col>
              </Row>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="总投资" prop="total_investors">
              <Input v-model="seeForm.total_investors" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="2019年计划投资" prop="plan_investors">
              <Input v-model="seeForm.plan_investors" placeholder="必填项" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="2019年计划形象进度" prop="plan_img_progress">
              <Input v-model="seeForm.plan_img_progress" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="start_month_img" prop="start_month_img_progress">
              <Input v-model="seeForm.month_img_progress" placeholder="" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem :label="start_month_act" prop="start_act_complete">
              <Input v-model="seeForm.start_act_complete" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="自开始累积完成投资" prop="acc_complete">
              <Input v-model="seeForm.acc_complete" placeholder="" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem :label="month_img" prop="month_img_progress">
              <Input v-model="seeForm.month_img_progress" placeholder="" readonly/>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="month_act" prop="month_act_complete">
              <Input v-model="seeForm.month_act_complete" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="seeForm.problem" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="开工时间" prop="start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                          v-model="seeForm.start_at" readonly></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="计划开工时间" prop="plan_start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                          v-model="seeForm.plan_start_at" readonly></DatePicker>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
              <Input v-model="seeForm.exp_preforma" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..." readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="备注" prop="marker">
              <Input v-model="seeForm.marker" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..." readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="9" style="margin-left: 20px;">
            <div class="demo-upload-list" v-for="item in defaultList">
              <template>
                <img :src="'storage/'+item.url">
                <div class="demo-upload-list-cover">
                  <Icon type="ios-eye-outline" @click.native="handleView(item.url)"></Icon>
                </div>
              </template>
              <template>
                <Progress hide-info></Progress>
              </template>
            </div>
            <Modal title="View Image" v-model="visible">
              <img :src="'storage/'+imgUrl" style="width: 100%">
            </Modal>
          </Col>
        </Row>
      </Form>
    </Modal>
    <!-- 编辑model------------------------------------------------------------------------------ -->
    <Modal
      v-model="editModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="编辑页面">
      <Form ref="editForm" :model="editForm" :label-width="150" :rules="editFormValidate">
        <Row>
          <Col span="11">
            <FormItem label="填报月份" prop="month">
              <DatePicker @on-change="changeMonth" type="month" placeholder="填报月份" format="yyyy-MM"
                          v-model="editForm.month" readonly></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="项目名称" prop="project_id">
              
              <Input v-model="editForm.project_id" placeholder="必填项" readonly></Input>
              <!-- <Select v-model="editForm.project_id"  @on-change="changeProject" readonly>
                <Option v-for="item in project_id" :value="item.id" :key="item.id" disabled>{{ item.title }}</Option>
              </Select> -->
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="项目编号" prop="project_num">
              <Input v-model="editForm.project_num" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="editForm.subject" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
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
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="总投资" prop="total_investors">
              <Input v-model="editForm.total_investors" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="2019年计划投资" prop="plan_investors">
              <Input v-model="editForm.plan_investors" placeholder="必填项" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="2019年计划形象进度" prop="plan_img_progress">
              <Input v-model="editForm.plan_img_progress" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="start_month_img" prop="start_month_img_progress">
              <Input v-model="editForm.month_img_progress" placeholder=""/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem :label="start_month_act" prop="start_act_complete">
              <Input v-model="editForm.start_act_complete" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="自开始累积完成投资" prop="acc_complete">
              <Input v-model="editForm.acc_complete" placeholder=""/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem :label="month_img" prop="month_img_progress">
              <Input v-model="editForm.month_img_progress" placeholder=""/>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="month_act" prop="month_act_complete">
              <Input v-model="editForm.month_act_complete" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="editForm.problem" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="开工时间" prop="start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                          v-model="editForm.start_at"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="计划开工时间" prop="plan_start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                          v-model="editForm.plan_start_at" readonly></DatePicker>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
              <Input v-model="editForm.exp_preforma" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="备注" prop="marker">
              <Input v-model="editForm.marker" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..."></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="9" style="margin-left: 20px;">
            <div class="demo-upload-list" v-for="item in defaultList">
              <template>
                <img :src="'storage/'+item.url">
                <div class="demo-upload-list-cover">
                  <Icon type="ios-eye-outline" @click.native="handleView(item.url)"></Icon>
                </div>
              </template>
              <template>
                <Progress hide-info></Progress>
              </template>
            </div>
            <Modal title="View Image" v-model="visible">
              <img :src="'storage/'+imgUrl" style="width: 100%">
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
    <!-- 审核model------------------------------------------------------------------------------ -->
    <Modal
      v-model="auditModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="审核页面">
      <Form ref="auditForm" :model="auditForm" :label-width="150" :rules="auditFormValidate">
        <Row>
          <Col span="11">
            <FormItem label="填报月份" prop="month">
              <DatePicker @on-change="changeMonth" type="month" placeholder="填报月份" format="yyyy-MM"
                          v-model="auditForm.month" readonly></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="项目名称" prop="project_id">
              
              <Input v-model="auditForm.project_id" placeholder="必填项" readonly></Input>
              <!-- <Select v-model="auditForm.project_id"  @on-change="changeProject" readonly>
                <Option v-for="item in project_id" :value="item.id" :key="item.id" disabled>{{ item.title }}</Option>
              </Select> -->
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="项目编号" prop="project_num">
              <Input v-model="auditForm.project_num" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="投资主体" prop="subject">
              <Input v-model="auditForm.subject" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="建设起止年限" prop="build_at">
              <Row>
                <Col span="11">
                  <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                              v-model="auditForm.build_start_at" readonly></DatePicker>
                </Col>
                <Col span="2" style="text-align: center">-</Col>
                <Col span="11">
                  <DatePicker type="month" placeholder="结束时间" format="yyyy-MM"
                              v-model="auditForm.build_end_at" readonly></DatePicker>
                </Col>
              </Row>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="总投资" prop="total_investors">
              <Input v-model="auditForm.total_investors" placeholder="必填项" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="2019年计划投资" prop="plan_investors">
              <Input v-model="auditForm.plan_investors" placeholder="必填项" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="2019年计划形象进度" prop="plan_img_progress">
              <Input v-model="auditForm.plan_img_progress" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="start_month_img" prop="start_month_img_progress">
              <Input v-model="auditForm.month_img_progress" placeholder="" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem :label="start_month_act" prop="start_act_complete">
              <Input v-model="auditForm.start_act_complete" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="自开始累积完成投资" prop="acc_complete">
              <Input v-model="auditForm.acc_complete" placeholder="" readonly/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem :label="month_img" prop="month_img_progress">
              <Input v-model="auditForm.month_img_progress" placeholder="" readonly/>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem :label="month_act" prop="month_act_complete">
              <Input v-model="auditForm.month_act_complete" placeholder="" readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="存在问题" prop="problem">
              <Input v-model="auditForm.problem" placeholder="" readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="开工时间" prop="start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                          v-model="auditForm.start_at" readonly></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="计划开工时间" prop="plan_start_at">
              <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                          v-model="auditForm.plan_start_at" readonly></DatePicker>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="11">
            <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
              <Input v-model="auditForm.exp_preforma" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..." readonly></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="备注" prop="marker">
              <Input v-model="auditForm.marker" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                     placeholder="请输入..." readonly></Input>
            </FormItem>
          </Col>
          <Col span="2"></Col>
          <Col span="9" style="margin-left: 20px;">
            <div class="demo-upload-list" v-for="item in defaultList">
              <template>
                <img :src="'storage/'+item.url">
                <div class="demo-upload-list-cover">
                  <Icon type="ios-eye-outline" @click.native="handleView(item.url)"></Icon>
                </div>
              </template>
              <template>
                <Progress hide-info></Progress>
              </template>
            </div>
            <Modal title="View Image" v-model="visible">
              <img :src="'storage/'+imgUrl" style="width: 100%">
            </Modal>
          </Col>
        </Row>
        <Row>
          <Col span="11">
            <FormItem label="审核" prop="marker">
              <Select v-model="auditForm.is_audit">
                <Option v-for="item in is_audit" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
      </Form>
      <div slot="footer">
        <Button
          @click="auditSubmit('auditForm')"
          :loading="submitLoading"
          type="primary"
          icon="ios-create-outline"
          style="margin-left:8px"
        >保存
        </Button>
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
    auditProjectProgress
  } from '../../../api/project';
  import './projectSchedule.css'

  export default {
    data() {
      return {
        btnDisable: true,
        searchForm: {
          project_id: ''
        },
        seeModal: false,
        editModal: false,
        auditModal: false,
        columns: [
          {
            type: 'selection',
            width: 60,
            align: 'center',
            fixed: 'left'
          },
          {
            title: '填报月份',
            key: 'month',
            width: 100,
            // fixed: 'left'
          },
          {
            title: '项目名称',
            key: 'project_id',
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
            width: 100
          },
          {
            title: '建设结束年限',
            key: 'build_end_at',
            width: 100
          },
          {
            title: '总投资',
            key: 'total_investors',
            width: 100
          },
          {
            title: '2019年计划投资',
            key: 'plan_investors',
            width: 100
          },
          {
            title: '2019年计划形象进度',
            key: 'plan_img_progress',
            width: 100
          },
          {
            title: '1-  月形象进度',
            key: 'start_month_img_progress',
            width: 100
          },
          {
            title: '1-  月实际完成投资',
            key: 'start_act_complete',
            width: 100
          },
          {
            title: 'x月形象进度',
            key: 'month_img_progress',
            width: 100
          },
          {
            title: 'x月实际完成投资',
            key: 'month_act_complete',
            width: 100
          },
          {
            title: '自开始累积完成投资',
            key: 'acc_complete',
            width: 140
          },
          {
            title: '存在问题',
            key: 'problem',
            width: 120
          },
          {
            title: '开工时间',
            key: 'start_at',
            width: 120
          },
          {
            title: '计划开工时间',
            key: 'plan_start_at',
            width: 120
          },
          {
            title: '操作',
            key: 'action',
            width: 200,
            fixed: 'right',
            align: 'center',
            render: (h, params) => {
              let edit_button = '';
              let is_button = '';
              if (params.row.is_audit == 0 || params.row.is_audit == 0) {
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
                      this.editForm.id = params.row.id;
                      let _editThis = this;
                      this.data.forEach(function (em) {
                        if (em.id === params.row.id) {
                          _editThis.editForm.month = em.month;
                          let em_project_id = em.project_id;
                          _editThis.project_id.forEach(function (em_id) {
                            if (em_project_id === em_id.id) {

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
                          _editThis.editForm.start_month_img_progress = em.start_month_img_progress;
                          _editThis.editForm.start_act_complete = em.start_act_complete;
                          _editThis.editForm.acc_complete = em.acc_complete;
                          _editThis.editForm.problem = em.problem;
                          _editThis.editForm.start_at = em.start_at;
                          _editThis.editForm.plan_start_at = em.plan_start_at;
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
                }, '编辑');
                is_button = h('Button', {
                  props: {
                    type: 'error',
                    size: 'small'
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      this.auditForm.id = params.row.id;
                      let _auditThis = this;
                      this.data.forEach(function (em) {
                        if (em.id === params.row.id) {
                          _auditThis.auditForm.month = em.month;
                          let em_project_id = em.project_id;
                          _auditThis.project_id.forEach(function (em_id) {
                            if (em_project_id === em_id.id) {

                              _auditThis.auditForm.project_id = em_id.title;
                            }
                          });
                          _auditThis.auditForm.project_num = em.project_num;
                          _auditThis.auditForm.subject = em.subject;
                          _auditThis.auditForm.build_start_at = em.build_start_at;
                          _auditThis.auditForm.build_end_at = em.build_end_at;
                          _auditThis.auditForm.total_investors = em.total_investors;
                          _auditThis.auditForm.plan_investors = em.plan_investors;
                          _auditThis.auditForm.plan_img_progress = em.plan_img_progress;
                          _auditThis.auditForm.month_img_progress = em.month_img_progress;
                          _auditThis.auditForm.month_act_complete = em.month_act_complete;
                          _auditThis.auditForm.start_month_img_progress = em.start_month_img_progress;
                          _auditThis.auditForm.start_act_complete = em.start_act_complete;
                          _auditThis.auditForm.acc_complete = em.acc_complete;
                          _auditThis.auditForm.problem = em.problem;
                          _auditThis.auditForm.start_at = em.start_at;
                          _auditThis.auditForm.plan_start_at = em.plan_start_at;
                          _auditThis.auditForm.exp_preforma = em.exp_preforma;

                          let img_pic = [];
                          if (em.img_progress_pic) {
                            let pics = em.img_progress_pic.split(",");
                            let i = 0;
                            pics.forEach(function (em_pic) {
                              img_pic.push({url: em_pic, name: i});
                              i++;
                            })
                          }
                          _auditThis.defaultList = img_pic;
                          _auditThis.auditForm.marker = em.marker;
                        }
                      });
                      this.getIsAudit();
                      this.auditModal = true;
                    }
                  }
                }, '审核');
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
                      let _seeThis = this;
                      this.data.forEach(function (em) {
                        if (em.id === params.row.id) {
                          _seeThis.seeForm.month = em.month;
                          let em_project_id = em.project_id;
                          _seeThis.project_id.forEach(function (em_id) {
                            if (em_project_id === em_id.id) {

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
                          _seeThis.seeForm.start_month_img_progress = em.start_month_img_progress;
                          _seeThis.seeForm.start_act_complete = em.start_act_complete;
                          _seeThis.seeForm.acc_complete = em.acc_complete;
                          _seeThis.seeForm.problem = em.problem;
                          _seeThis.seeForm.start_at = em.start_at;
                          _seeThis.seeForm.plan_start_at = em.plan_start_at;
                          _seeThis.seeForm.exp_preforma = em.exp_preforma;

                          let img_pic = [];
                          if (em.img_progress_pic) {
                            console.log(em);

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
                edit_button,
                is_button
              ]);
            }
          }
        ],
        data: [],
        tableLoading: true,
        loading: false,
        start_month_img: '1-  月形象进度',
        start_month_act: '1- 月实际完成投资',
        month_img: 'x月形象进度',
        month_act: 'x月实际完成投资',
        year_investors: '2019年计划投资',
        year_img: '2019年形象进度',
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
          start_month_img_progress: '',
          start_act_complete: '',
          month_img_progress: '',
          month_act_complete: '',
          acc_complete: '',
          problem: '',
          start_at: '',
          plan_start_at: '',
          exp_preforma: '',
          img_progress_pic: '',
          marker: '',
          is_audit: ''
        },
        auditForm: {
          id: '',
          month: '',
          project_id: '',
          project_num: '',
          subject: '',
          build_start_at: '',
          build_end_at: '',
          total_investors: '',
          plan_investors: '',
          plan_img_progress: '',
          start_month_img_progress: '',
          start_act_complete: '',
          month_img_progress: '',
          month_act_complete: '',
          acc_complete: '',
          problem: '',
          start_at: '',
          plan_start_at: '',
          exp_preforma: '',
          img_progress_pic: '',
          marker: '',
          is_audit: ''
        },
        seeForm: {
          month: '',
          project_id: '',
          project_num: '',
          subject: '',
          build_start_at: '',
          build_end_at: '',
          total_investors: '',
          plan_investors: '',
          plan_img_progress: '',
          start_month_img_progress: '',
          start_act_complete: '',
          month_img_progress: '',
          month_act_complete: '',
          acc_complete: '',
          problem: '',
          start_at: '',
          plan_start_at: '',
          exp_preforma: '',
          img_progress_pic: '',
          marker: '',
          is_audit: ''
        },
        editForm: {
          id: '',
          month: '',
          project_id: '',
          project_num: '',
          subject: '',
          build_start_at: '',
          build_end_at: '',
          total_investors: '',
          plan_investors: '',
          plan_img_progress: '',
          start_month_img_progress: '',
          start_act_complete: '',
          month_img_progress: '',
          month_act_complete: '',
          acc_complete: '',
          problem: '',
          start_at: '',
          plan_start_at: '',
          exp_preforma: '',
          img_progress_pic: '',
          marker: ''
        },
        index: 1,
        modal: false,
        submitLoading: false,
        editFormValidate: {
          // 表单验证规则
          month: [
            {required: true, type: 'date', message: "填报月份不能为空", trigger: "change"}
          ],
          // project_id: [
          //   {required: true, message: '项目名称不能为空', trigger: 'change'}
          // ],
          investors: [
            {required: true, message: '投资主体不能为空', trigger: 'blur'}
          ],
          build_start_at: [
            {required: true, type: 'date', message: '建设开始年限不能为空', trigger: 'change'}
          ],
          build_end_at: [
            {required: true, type: 'date', message: '建设结束年限不能为空', trigger: 'change'}
          ],
          total_investors: [
            {required: true, message: '总投资不能为空', trigger: 'blur'}
          ],
          plan_investors: [
            {required: true, message: '2019年计划投资不能为空', trigger: 'blur'}
          ],
          plan_start_at: [
            {required: true, type: 'date', message: '计划开工时间不能为空', trigger: 'change'}
          ]
        },
        auditFormValidate: {},
        formValidate: {
          // 表单验证规则
          month: [
            {required: true, type: 'date', message: "填报月份不能为空", trigger: "change"}
          ],
          // project_id: [
          //   {required: true, message: '项目名称不能为空', trigger: 'change'}
          // ],
          investors: [
            {required: true, message: '投资主体不能为空', trigger: 'blur'}
          ],
          build_start_at: [
            {required: true, type: 'date', message: '建设开始年限不能为空', trigger: 'change'}
          ],
          build_end_at: [
            {required: true, type: 'date', message: '建设结束年限不能为空', trigger: 'change'}
          ],
          total_investors: [
            {required: true, message: '总投资不能为空', trigger: 'blur'}
          ],
          plan_investors: [
            {required: true, message: '2019年计划投资不能为空', trigger: 'blur'}
          ],
          plan_start_at: [
            {required: true, type: 'date', message: '计划开工时间不能为空', trigger: 'change'}
          ]
        },
        project_id: [],
        imgName: '',
        visible: false,
        uploadList: [],
        imgUrl: '',
        defaultList: [],
        is_audit: []
      }
    },
    methods: {
      init() {
        this.$refs.form.resetFields();// 获取项目名称
        this.getProjectId();
        this.getProjectScheduleList();
      },
      getProjectScheduleList() {
        let search_project_id = this.searchForm.project_id;
        this.tableLoading = true;
        projectProgressList({search_project_id: search_project_id}).then(res => {
          this.data = res.result;
          this.tableLoading = false;
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
        let _this = this;
        this.project_id.forEach(function (em) {
          if (em.id === e) {
            _this.form.subject = em.subject;
            _this.form.project_num = em.num;
            _this.form.build_start_at = em.plan_start_at;
            _this.form.build_end_at = em.plan_end_at;
            _this.form.total_investors = em.amount;
            _this.form.plan_start_at = em.plan_start_at;
            if (_this.form.month) {
              projectPlanInfo({month: _this.form.month}).then(res => {
                _this.form.plan_investors = res.result.amount;
                _this.form.plan_img_progress = res.result.image_progress;
              });
            }
          }
        })
      },
      changeMonth(e) {
        this.start_month_img = '开始到' + e + ' 月形象进度';
        this.start_month_act = '开始到' + e + ' 月实际完成投资';
        this.month_img = e + ' 月形象进度';
        this.month_act = e + ' 月实际完成投资';
        this.year_investors = e.substring(0, 4) + '年计划投资';
        this.year_img = e.substring(0, 4) + '年形象进度';
        if (this.form.project_id) {
          projectPlanInfo({month: e}).then(res => {
            this.form.plan_investors = res.result.amount;
            this.form.plan_img_progress = res.result.image_progress;
          });
        }
      },
      handleReset() {
        this.$refs[name].resetFields();
        this.$refs.upload.clearFiles()
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
      }, editSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            editProjectProgress(this.editForm).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("修改成功");
                this.editModal = false;
                this.init();
              } else {
                this.$Message.error('修改失败!');
              }
            });
          }
        })
      }, auditSubmit(name) {
        this.submitLoading = true;
        auditProjectProgress(this.auditForm).then(res => {
          this.submitLoading = false;
          if (res.result) {
            this.$Message.success("修改成功");
            this.auditModal = false;
            this.init();
          } else {
            this.$Message.error('修改失败!');
          }
        });
      },
      cancel() {
        this.$refs.form.resetFields();
        this.form.group_id = this.checkedDefaultRole;
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
          desc: '文件太大，不超过2米'
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
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
