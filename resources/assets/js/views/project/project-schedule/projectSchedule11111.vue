<template>
  <div class="search">
    <Card>
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
                            <Select v-model="form.project_id"  @on-change="changeProject">
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
                                  <DatePicker type="month" placeholder="开始时间" format="yyyy-mm"
                                              v-model="form.build_start_at"></DatePicker>
                              </Col>
                              <Col span="2" style="text-align: center">-</Col>
                              <Col span="11">
                                  <DatePicker type="month" placeholder="结束时间" format="yyyy-mm"
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
                      <FormItem label="2019年计划投资" prop="plan_investors">
                          <Input v-model="form.plan_investors" placeholder="必填项"/>
                      </FormItem>
                  </Col>
              </Row>
              <Row>
                  <Col span="11">
                      <FormItem label="2019年计划形象进度" prop="plan_img_progress">
                          <Input v-model="form.plan_img_progress" placeholder=""></Input>
                      </FormItem>
                  </Col>
                  <Col span="2"></Col>
                  <Col span="11">
                      <FormItem :label="month_img" prop="month_img_progress">
                          <Input v-model="form.month_img_progress" placeholder=""/>
                      </FormItem>
                  </Col>
              </Row>
              <Row>
                  <Col span="11">
                      <FormItem :label="month_act" prop="act_complete">
                          <Input v-model="form.act_complete" placeholder=""></Input>
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
            <Button
                @click="submitF('form')"
                :loading="submitLoading"
                type="primary"
                icon="ios-create-outline"
                style="margin-right:5px"
            >保存
            </Button>
            <Button @click="handleReset">重置</Button>
            </Form-item>
        </Form>
    </Card>
  </div>
</template>

<script>
  import "./projectSchedule.css";
  import {initProjectInfo,projectProgress} from "../../../api/project";

  export default {
    name: "",
    data() {
      return {
        isCheck: true,
        month_img:'1-  月形象进度',
        month_act:'1- 月实际完成投资',
        form: {
          month:'',
          project_id:'',
          subject:'',
          build_start_at:'',
          build_end_at:'',
          total_investors:'',
          plan_investors:'',
          plan_img_progress:'',
          month_img_progress:'',
          act_complete:'',
          acc_complete:'',
          problem:'',
          start_at:'',
          plan_start_at:'',
          exp_preforma:'',
          img_progress_pic:'',
          marker:'',
        },
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
            {required: true, message: '建设开始年限不能为空', trigger: 'change'}
          ],
          build_end_at: [
            {required: true, message: '建设结束年限不能为空', trigger: 'change'}
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
        submitLoading: false,
        project_id:[],
        imgName: '',
        visible: false,
        uploadList: []
      };
    },
    methods: {
      init() {
        // 获取项目名称
        this.getProjectId();
      },
      getProjectId(){
        initProjectInfo().then(res => {
          this.project_id=res.result;
        });
      },
      changeProject(e){
        let _this = this;
        this.project_id.forEach(function (em) {
          if (em.id===e) {
            _this.form.subject=em.subject;
            _this.form.project_num=em.num;
          }
        })
      },
      changeMonth(e) {
          this.month_img=e+' 月形象进度'
          this.month_act=e+' 月实际完成投资'
      },
      handleReset() {
        this.$refs.form.resetFields();
      },
      submitF(name) {
          this.$refs[name].validate((valid) => {
            if (valid) {
              this.submitLoading=true;
              projectProgress(this.form).then(res => {
                this.submitLoading=false;
                if(res.result){
                  this.$Message.success("填报成功");
                  this.init();
                  this.modalVisible = false;
                  this.$refs.form.resetFields();
                }else{
                  this.$Message.error('填报失败!');
                }
            });
          }
        })
      },//上传图片
      handleSuccess (res, file) {
        this.form.img_progress_pic=this.form.img_progress_pic+','+res.result;
      },
      handleFormatError (file) {
          this.$Notice.warning({
              title: '文件格式不正确',
              desc: '文件格式不正确，请选择JPG或PNG'
          });
      },
      handleMaxSize (file) {
          this.$Notice.warning({
              title: '超出文件大小限制',
              desc: '文件太大，不超过2米'
          });
      },
      handleBeforeUpload () {
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
  };
</script>