<template>
  <Card style="padding: 0 50px;">
    <Form ref="previewFormValidate" :model="previewForm" :label-width="143">
      <Divider>
        <h4>基础信息</h4>
      </Divider>
      <Row>
        <Col span="12">
          <FormItem label="项目名称" prop="title">
            <Input v-model="previewForm.title" placeholder readonly/>
          </FormItem>
        </Col>
        <Col span="12">
          <FormItem label="项目编号" prop="num">
            <Input v-model="previewForm.num" placeholder readonly></Input>
          </FormItem>
        </Col>
      </Row>
      <Row>
        <Col span="12">
          <FormItem label="项目类型" prop="type">
            <Input v-model="previewForm.type" placeholder readonly/>
          </FormItem>
        </Col>
        <Col span="12">
          <FormItem label="投资主体" prop="subject">
            <Input v-model="previewForm.subject" placeholder readonly/>
          </FormItem>
        </Col>
      </Row>
      <Row>
        <Col span="12">
          <FormItem label="建设性质" prop="build_type">
            <Input v-model="previewForm.build_type" placeholder readonly></Input>
          </FormItem>
        </Col>
        <Col span="12">
          <FormItem label="承建单位" prop="unit">
            <Input v-model="previewForm.unit" placeholder readonly></Input>
          </FormItem>
        </Col>
      </Row>
      <Row>
        <Col span="12">
          <FormItem label="资金来源" prop="money_from">
            <Input v-model="previewForm.money_from" placeholder readonly></Input>
          </FormItem>
        </Col>
        <Col span="12">
          <FormItem label="项目金额(万元)" prop="amount">
            <Input v-model="previewForm.amount" placeholder readonly/>
          </FormItem>
        </Col>
      </Row>
      <Row>
        <Col span="12">
          <FormItem label="土地费用(万元)" prop="land_amount">
            <Input v-model="previewForm.land_amount" placeholder readonly/>
          </FormItem>
        </Col>
        <Col span="12">
          <FormItem label="项目状态" prop="status">
            <Input v-model="previewForm.status" placeholder readonly></Input>
          </FormItem>
        </Col>
      </Row>
      <Row>
        <Col span="12">
          <FormItem label="项目标识(是否为国民经济计划)" prop="is_gc">
            <Input v-model="previewForm.is_gc" placeholder readonly></Input>
          </FormItem>
        </Col>
        <Col span="12">
          <FormItem label="国民经济计划分类" prop="nep_type">
            <Input v-model="previewForm.nep_type" placeholder readonly></Input>
          </FormItem>
        </Col>
      </Row>
      <Row>
        <Col span="12">
          <FormItem label="计划开始时间" prop="plan_start_at">
            <DatePicker
              type="month"
              readonly
              placeholder
              format="yyyy年MM月"
              v-model="previewForm.plan_start_at"
            ></DatePicker>
          </FormItem>
        </Col>
        <Col span="12">
          <FormItem label="计划结束时间" prop="plan_end_at">
            <DatePicker
              type="month"
              readonly
              placeholder
              format="yyyy年MM月"
              v-model="previewForm.plan_end_at"
            ></DatePicker>
          </FormItem>
        </Col>
      </Row>
      <Row>
        <FormItem label="项目概况(建设规模及主要内容)" prop="description">
          <Input v-model="previewForm.description" type="textarea" :rows="4" placeholder readonly></Input>
        </FormItem>
      </Row>
      <Divider>
        <h4>投资计划</h4>
      </Divider>
      <div v-for="(item, index) in previewForm.projectPlan">
        <Divider orientation="left">
          <h5 style="color: #2d8cf0;">{{item.date}}年项目投资计划</h5>
        </Divider>
        <Row>
          <Col span="24">
            <FormItem label="计划投资金额(万元)" :prop="'projectPlan.' + index + '.amount'">
              <Input v-model="item.amount" placeholder readonly/>
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem label="计划形象进度" :prop="'projectPlan.' + index + '.image_progress'">
              <Input v-model="item.image_progress" type="textarea" :rows="1" placeholder readonly></Input>
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
              <Input type="text" placeholder v-model="ite.amount" readonly class="monthInput"/>
            </Col>
            <Col span="17">
              <Input
                type="textarea"
                :rows="1"
                placeholder
                v-model="ite.image_progress"
                readonly
                class="monthInput"
              />
            </Col>
          </div>
        </Row>
      </div>
    </Form>
    <Spin size="large" fix v-if="infoLoading"></Spin>
  </Card>
</template>
<script>
import { getProjectById } from "../../../api/project";
export default {
  data() {
    return {
      infoLoading: false,
      previewForm: {},
      id: 0
    };
  },
  watch: {
    $route(to, from) {
      if (to.path === "/projects/preview") {
        this.loadData(this.$route.query.id);
      }
    }
  },
  created() {
    this.loadData(this.$route.query.id);
  },
  methods: {
    loadData(id) {
      this.infoLoading = true;
      getProjectById(id).then(res => {
        this.previewForm = res.result;
        this.infoLoading = false;
      });
    },
    handleSubmit(name) {
      this.$refs[name].validate(valid => {
        if (valid) {
          this.$Message.success("Success!");
        } else {
          this.$Message.error("Fail!");
        }
      });
    }
  }
};
</script>
