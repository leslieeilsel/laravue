<template>
  <div class="search">
    <Card>
        <Form ref="form" :model="form" :label-width="150" :rules="formValidate">
              <Row>
                  <Col span="11">
                        <FormItem label="填报月份" prop="month">
                            <Select v-model="form.month" @on-change="changeMonth">
                              <Option v-for="item in month" :value="item.value" :key="item.value">{{ item.label }}</Option>
                            </Select>
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
                      <FormItem label="投资主体" prop="subject">
                          <Input v-model="form.subject" placeholder="必填项" readonly></Input>
                      </FormItem>
                  </Col>
              </Row>
              <Row>
                  <Col span="11">
                      <FormItem label="建设起止年限" prop="build_at">
                          <Row>
                              <Col span="11">
                                  <DatePicker type="year" placeholder="开始时间" format="yyyy"
                                              v-model="form.build_start_at"></DatePicker>
                              </Col>
                              <Col span="2" style="text-align: center">-</Col>
                              <Col span="11">
                                  <DatePicker type="year" placeholder="结束时间" format="yyyy"
                                              v-model="form.build_end_at"></DatePicker>
                              </Col>
                          </Row>
                      </FormItem>
                  </Col>
                  <Col span="2"></Col>
                  <Col span="11">
                      <FormItem label="总投资" prop="total_investors">
                          <Input v-model="form.total_investors" placeholder="必填项"></Input>
                      </FormItem>
                  </Col>
              </Row>
              <Row>
                  <Col span="11">
                      <FormItem label="2019年计划投资" prop="plan_investors">
                          <Input v-model="form.plan_investors" placeholder="必填项"/>
                      </FormItem>
                  </Col>
                  <Col span="2"></Col>
                  <Col span="11">
                      <FormItem label="2019年计划形象进度" prop="plan_img_progress">
                          <Input v-model="form.plan_img_progress" placeholder=""></Input>
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
                      <FormItem :label="month_act" prop="act_complete">
                          <Input v-model="form.act_complete" placeholder=""></Input>
                      </FormItem>
                  </Col>
              </Row>
              <Row>
                  <Col span="11">
                      <FormItem label="自开始累积完成投资" prop="acc_complete">
                          <Input v-model="form.acc_complete" placeholder=""/>
                      </FormItem>
                  </Col>
                  <Col span="2"></Col>
                  <Col span="11">
                      <FormItem label="存在问题" prop="problem">
                          <Input v-model="form.problem" placeholder=""></Input>
                      </FormItem>
                  </Col>
              </Row>
              <Row>
                  <Col span="11">
                      <FormItem label="开工时间" prop="start_at">
                          <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                                      v-model="form.start_at"></DatePicker>
                      </FormItem>
                  </Col>
                  <Col span="2"></Col>
                  <Col span="11">
                      <FormItem label="计划开工时间" prop="plan_start_at">
                          <DatePicker type="month" placeholder="开始时间" format="yyyy-MM"
                                      v-model="form.plan_start_at"></DatePicker>
                      </FormItem>
                  </Col>
              </Row>
              <Row>
                  <Col span="11">
                      <FormItem label="土地征收情况及前期手续办理情况" prop="exp_preforma">
                          <Input v-model="form.exp_preforma" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                              placeholder="请输入..."></Input>
                      </FormItem>
                  </Col>
                  <Col span="2"></Col>
                  <Col span="11">
                      <FormItem label="备注" prop="marker">
                          <Input v-model="form.marker" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                              placeholder="请输入..."></Input>
                      </FormItem>
                  </Col>
              </Row>
              <Row>
                  <Col span="11">
                      <FormItem label="形象进度照片" prop="img_progress_pic">
                        <Upload
                            ref="upload"
                            :show-upload-list="false"
                            :default-file-list="defaultList"
                            :on-success="handleSuccess"
                            multiple
                            :format="['jpg','jpeg','png']"
                            :max-size="2048"
                            action="//jsonplaceholder.typicode.com/posts/">
                            <Button icon="ios-cloud-upload-outline">Upload files</Button>
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
    name: "tree",
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
          marker:''
        },
        formValidate: {
          // 表单验证规则
          month: [
            {required: true, message: "填报月份不能为空", trigger: "change"}
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
        month:[
            {value: '0',label: '请选择'},
            {value: '1',label: '1月'},
            {value: '2',label: '2月'},
            {value: '3',label: '3月'},
            {value: '4',label: '4月'},
            {value: '5',label: '5月'},
            {value: '6',label: '6月'},
            {value: '7',label: '7月'},
            {value: '8',label: '8月'},
            {value: '9',label: '9月'},
            {value: '10',label: '10月'},
            {value: '11',label: '11月'},
            {value: '12',label: '12月'},
        ],
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
          }
        })
      },
      changeMonth(e) {
          this.month_img='1-'+e+' 月形象进度'
          this.month_act='1-'+e+' 月实际完成投资'
      },
      handleReset() {
        this.$refs.form.resetFields();
      },
      submitF(name) {
        this.$refs
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
      handleView (name) {
          this.imgName = name;
          this.visible = true;
      },
      handleRemove (file) {
          const fileList = this.$refs.upload.fileList;
          this.$refs.upload.fileList.splice(fileList.indexOf(file), 1);
      },
      handleSuccess (res, file) {
          file.url = 'https://o5wwk8baw.qnssl.com/7eb99afb9d5f317c912f08b5212fd69a/avatar';
          file.name = '7eb99afb9d5f317c912f08b5212fd69a';
      },
      handleFormatError (file) {
          this.$Notice.warning({
              title: 'The file format is incorrect',
              desc: 'File format of ' + file.name + ' is incorrect, please select jpg or png.'
          });
      },
      handleMaxSize (file) {
          this.$Notice.warning({
              title: 'Exceeding file size limit',
              desc: 'File  ' + file.name + ' is too large, no more than 2M.'
          });
      },
      handleBeforeUpload () {
          const check = this.uploadList.length < 5;
          if (!check) {
              this.$Notice.warning({
                  title: 'Up to five pictures can be uploaded.'
              });
          }
          return check;
      }
    },
    mounted() {
      this.init();
      this.uploadList = this.$refs.upload.fileList;
    }
  };
</script>