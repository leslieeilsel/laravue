<template>
  <Card>
    <Row>
      <Form ref="searchForm" :model="searchForm" inline :label-width="100" class="search-form">
        <FormItem label="服务时间" prop="date_time">
            <DatePicker type="date" placeholder="服务时间" v-model="searchForm.date_time" style="width: 200px">
            </DatePicker>
        </FormItem>
        <FormItem style="margin-left:-70px;" class="br">
          <Button @click="getSuperviseServiceList" type="primary" icon="ios-search">检索</Button>
        </FormItem>
      </Form>
    </Row>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add" v-if="isShowButton">服务信息填报</Button>
    </p>
    <Table type="selection" stripe border :columns="columns" :data="data" :loading="tableLoading"></Table>
    <Row type="flex" justify="end" class="page">
      <Page
        :total="dataCount"
        :page-size="searchForm.pageSize"
        @on-change="changePage"
        @on-page-size-change="changePageSize"
        show-total
        :current="searchForm.pageNumber"/>
    </Row>
    <!-- model -->
    <Modal
      :mask-closable="false"
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="服务信息填报">
      <Form ref="form" :model="form" :label-width="160">
        <Row>
          <Col span="12">
            <FormItem label="电话" prop="phone">
              <Input v-model="form.phone" placeholder="" ></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="位置" prop="position">
              <Input v-model="form.position" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <FormItem label="视频" prop="video">
            <Upload
              ref="upload"
              :disabled="upbtnVideoDisabled"
              name="video"
              :show-upload-list="true"
              :before-upload="beforeUpload"
              :on-success="handleSuccessVideo"
              :on-error="handleErrorVideo"
              multiple
              :format="['mp4','mov']"
              :on-format-error="handleFormatError"
              action="/api/value/uploadVideo">
              <Button  type="primary" icon="ios-cloud-upload-outline">上传</Button>
            </Upload>
            <!--  :loading="uploadLoading"
              <div v-for="(item,index) in defaultList">
              {{item}}
            </div> -->
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
    <!-- model -->
    <Modal
      :mask-closable="false"
      v-model="editModal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="80%"
      :footer-hide='true'
      title="修改活动计划">
        <Row>
          <Col span="8" style="border-right:1px solid #e8eaec;padding-right: 10px;">
            <Divider>客户服务</Divider>
            <Form ref="editForm" :model="editForm" :label-width="100" :rules="editFormValidate">
              <FormItem label="用户电话" prop="phone">
                <Input v-model="editForm.phone" placeholder="" readonly/>
              </FormItem>
              <FormItem label="位置" prop="position">
                <Input v-model="editForm.position" placeholder="" readonly/>
              </FormItem>
              <FormItem label="时间" prop="date_time">
                <Input v-model="editForm.date_time" placeholder="" readonly/>
              </FormItem>
              <FormItem label="服务人员区域" prop="area">
                <Input v-model="editForm.area" placeholder="" readonly/>
              </FormItem>
              <FormItem label="服务人员工号" prop="job_num">
                <Input v-model="editForm.job_num" placeholder="" readonly/>
              </FormItem>
              <FormItem label="服务人员姓名" prop="ename">
                <Input v-model="editForm.ename" placeholder="" readonly/>
              </FormItem>
              <Divider>服务打分</Divider>
              <FormItem label="服务打分" prop="service_grade">
                  <Table ref="table" type="selection" max-height="500" width='80%' stripe border :columns="columns_s" :data="dict.supervise_service" @on-selection-change="changeSelect"></Table>
                <!-- <CheckboxGroup v-model="editForm.service_grade">
                    <Checkbox v-for="item in dict.supervise_service" :label="item.value" :key="item.value">
                        <span>{{ item.title }}</span>
                    </Checkbox>
                </CheckboxGroup> -->
              </FormItem>
              <!-- <FormItem label="合计: ">
                {{service_grade}}
              </FormItem> -->
              <div style="width:65%;">
                <h2 style="text-align: center;">合计 :  <font style="color:red">{{service_grade}}</font> 分</h2>
              </div>
              <Form-item>
                <Button
                  style="margin-right:5px"
                  @click="editSubmit('editForm')"
                  :loading="submitLoading"
                  type="primary"
                  icon="ios-create-outline"
                >保存
                </Button>
                <Button @click="handleReset">重置</Button>
              </Form-item>
            </Form>
          </Col>
          <Col span="16">
            <Divider>监控视频</Divider>
            <div>
              <Form ref="videoForm" :model="videoForm" :label-width="100">
                <FormItem label="选择切换视频" prop="switchVideo" style="width: 50%;">
                  <Select v-model="videoForm.switchVideo" filterable  @on-change="chanageVideo">
                    <Option v-for="(item,index) in switchVideoData" :value="index" :key="index">视频  {{ index+1 }}</Option>
                  </Select>
                </FormItem>
              </Form>
            </div>
            <div style="text-align:center">
              <test-video-player v-if="load" ret="TestVideoPlayer" @play="onPlayerPlay($event)" class="vjs-custom-skin" style="width: 95%;height: 95%;display:inline-block" :options="videoOptions"></test-video-player>
            </div>
          </Col>
        </Row>
    </Modal>
  </Card>
</template>
<script>
  import {
    superviseServiceList,
    superviseServiceAdd,
    superviseServiceEdit,
    superviseServiceDel,
    dictDataSupervise
  } from '../../../api/value';
  import './superviseService.css'
  import TestVideoPlayer from "../../my-components/test-video-player";
  import 'videojs-flash';

  export default {
    components: {
      TestVideoPlayer
    },
    data() {
      return {
        dataCount: 0,   // 总条数
        columns: [
          {
            type: 'index2',
            width: 50,
            align: 'center',
            fixed: 'left',
            render: (h, params) => {
              return h('span', params.index + (this.searchForm.pageNumber - 1) * this.searchForm.pageSize + 1);
            }
          },
          {
            title: '电话',
            key: 'phone',
            width: 200,
            // fixed: 'left',
            align: "center"
          },
          {
            title: '位置',
            key: 'position',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '时间',
            key: 'date_time',
            width: 220,
            align: 'left'
          },
          {
            title: '服务人员姓名',
            key: 'ename',
            width: 200,
            align: "center"
          },
          {
            title: '服务人员工号',
            key: 'job_num',
            width: 200,
            align: "left"
          },
          {
            title: '服务人员区域',
            key: 'area',
            width: 200,
            align: "center"
          },
          {
            title: '服务打分',
            key: 'service_grade',
            width: 300,
            align: "left"
          },
          {
            title: '操作',
            key: 'action',
            width: 280,
            fixed: 'right',
            align: 'center',
            render: (h, params) => {
              let editButton=true;
              let delButton=true;
              (this.users.group_id===8||this.users.group_id===4)?editButton=false:editButton=true;
              (this.users.group_id===6||this.users.group_id===4)?delButton=false:delButton=true;
              return h('div', [
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
                      console.log(params.row);
                      this.service_grade=params.row.service_grade;
                      this.editForm.id=params.row.id;
                      this.editForm.area=params.row.area;
                      this.editForm.date_time=params.row.date_time;
                      this.editForm.ename=params.row.ename;
                      this.editForm.job_num=params.row.job_num;
                      this.editForm.phone=params.row.phone;
                      this.editForm.position=params.row.position;
                      if(params.row.video){
                      let videoArr=params.row.video.split(",");
                        this.switchVideoData=videoArr;
                        // switchVideo
                        this.load = false;
                        this.videoOptions.sources[0].src =videoArr[0];
                        this.$nextTick(() => {
                          this.load = true;
                        });
                        this.$refs.table.$refs.tbody.data.forEach(v => {
                          this.$refs.table.$refs.tbody.objData[v._index]._isChecked = false;
                        });
                      }
                      if(params.row.service_grade>0){
                        let service_grade_id=eval("("+params.row.service_grade_id+")");
                        service_grade_id.forEach(v => {
                          this.$refs.table.$refs.tbody.objData[v.id-1]._isChecked = true;
                        });
                      }
                      this.editModal = true;
                      // $('.ivu-upload-list').remove();
                    }
                  }
                }, '考核'),
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
                        content: "您确认要删除这个数据？",
                        onOk: () => {
                          superviseServiceDel({id: params.row.id}).then(res => {
                            if (res.result === true) {
                              this.$Message.success("删除成功");
                              this.init();
                            } else {
                              this.$Message.error("删除失败");
                            }
                            this.$Modal.remove();
                          });
                        }
                      });
                    }
                  }
                }, '删除')
              ])
            }
          }
        ],
        columns_s: [
          {
              type: 'selection',
              width: 60,
              align: 'center'
          },
          {
            title: '项目',
            key: 'title',
            width: 100,
            // fixed: 'left',
            align: "center"
          },
          {
            title: '分值',
            key: 'service_grade',
            // fixed: 'left',
            width: 100,
          }
        ],
        data: [],
        isShowButton: false,
        tableLoading: true,
        searchForm: {
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        dictName: {
          supervise_service: '服务打分',
        },
        dict: {
          supervise_service:[],
        },
        submitLoading: false,
        loading:false,
        uploadLoading:false,
        modal: false,
        editModal:false,
        form: {
          phone: '',
          position: '',
          video: ''
        },
        editForm:{
          id:0,
          service_grade:[]
        },
        editFormValidate: {
          // 表单验证规则
        },
        switchVideoData:[],
        videoForm:{switchVideo:0},
        service_grade:0,
        upbtnVideoDisabled: false,
        users:{},
        defaultList:[],
        load : true,
        videoOptions: {
          playbackRates: [0.7, 1.0, 1.5, 2.0], //播放速度
          autoplay: true, //如果true,浏览器准备好时开始回放。
          muted: false, // 默认情况下将会消除任何音频。
          loop: true, // 导致视频一结束就重新开始。
          preload: 'auto', // 建议浏览器在<video>加载元素后是否应该开始下载视频数据。auto浏览器选择最佳行为,立即开始加载视频（如果浏览器支持）
          language: 'zh-CN',
          aspectRatio: '16:9', // 将播放器置于流畅模式，并在计算播放器的动态大小时使用该值。值应该代表一个比例 - 用冒号分隔的两个数字（例如"16:9"或"4:3"）
          fluid: true, // 当true时，Video.js player将拥有流体大小。换句话说，它将按比例缩放以适应其容器。
          controls: true,
          sources: [
            {
              src:"",
              type: "video/mp4"
            }
          ],
          notSupportedMessage: '您还没有上传视频', //允许覆盖Video.js无法播放媒体源时显示的默认信息。
          // poster: "./fx1.png", //你的封面地址
          controlBar: {
            timeDivider: true,
            durationDisplay: true,
            remainingTimeDisplay: false,
            fullscreenToggle: true  //全屏按钮
          },

        }
      }
    },
    methods: {
      init() {
        this.users=this.$store.getters.user;
        this.isShowButton=(this.users.group_id===6||this.users.group_id===4)
        this.getDictData();
        this.getSuperviseServiceList();
      },
      getSuperviseServiceList() {
        this.tableLoading = true;
        superviseServiceList(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getSuperviseServiceList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getSuperviseServiceList();
      },
      getDictData() {
        dictDataSupervise().then(res => {
          if (res.result) {
            this.dict.supervise_service = res.result;            
          }
        });
      },
      handleReset(name) {
        this.$refs[name].resetFields();
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            superviseServiceAdd(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("服务信息填报成功");
                this.modal = false;
                this.uploadLoading=false;
                this.defaultList=[];
                this.init();
              } else {
                this.$Message.error('服务信息填报失败!');
              }
            });
          }
        })
      },
      editSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;          
            superviseServiceEdit(this.editForm).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("服务信息修改成功");   
                this.load = false;
                this.videoOptions.sources[0].src = '';
                this.$nextTick(() => {
                  this.load = true;
                });
                this.editModal = false;
                this.getSuperviseServiceList();
                this.cancel();
                this.lists=[];
              } else {
                this.$Message.error('服务信息修改失败!');
              }
            });
          }
        })
      },
      cancel() {
        this.$refs.form.resetFields();
        this.$refs.upload.clearFiles();
        this.uploadLoading=false;
        this.defaultList=[];
        this.load = false;
        this.videoOptions.sources[0].src =''
        this.$nextTick(() => {
          this.load = true;
        });
      },
      beforeUpload(){
        this.uploadLoading=true;
        this.upbtnVideoDisabled=true;
        return true;
      },
      handleSuccessVideo(res, file) {        
        this.defaultList.push(res.result)
        if (this.form.video) {
          this.form.video = this.form.video + ',' + res.result;
        } else {
          this.form.video = res.result;
        }
        this.upbtnVideoDisabled=false;
        this.uploadLoading=false;
      },handleErrorVideo(){
        this.$Notice.error({
          title: '文件上传失败',
          desc: '文件上传失败'
        });
      },
      handleFormatError(file) {
        this.$Notice.warning({
          title: '文件格式不正确',
          desc: '文件格式不正确，请选择mp4或mov'
        });
      },
      changeSelect(data){
        this.editForm.service_grade=data;
        let grade=0;
        data.forEach(v=>{
          grade=parseInt(grade)+parseInt(v.service_grade);
        })
        this.service_grade=grade;
      },
      chanageVideo(id){        
        this.load = false;
        this.videoOptions.sources[0].src =this.switchVideoData[id];
        this.$nextTick(() => {
          this.load = true;
        });
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
