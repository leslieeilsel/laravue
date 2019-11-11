<template>
  <div class="search">
    <Card>
      <Row>
        <Col span="6">
          <Alert show-icon>
            当前选择编辑：
            <span class="select-title">{{editTitle}}</span>
            <a class="select-clear" v-if="departmentForm.id" @click="cancelEdit">取消选择</a>
          </Alert>
          <div class="tree-bar">
            <Tree
              ref="tree"
              :data="data"
              @on-check-change="changeSelect"
              @on-select-change="selectTree"
            ></Tree>
          </div>
          <Spin size="large" fix v-if="loading"></Spin>
        </Col>
        <Col span="9">
          <Form ref="departmentForm" :model="departmentForm" :label-width="100" :rules="departmentFormValidate">
            <FormItem label="渠道名称" prop="title">
              <Input v-model="departmentForm.title" placeholder="" readonly/>
            </FormItem>
            <FormItem label="联系人" prop="name">
              <Input v-model="departmentForm.name" placeholder="" readonly/>
            </FormItem>
            <FormItem label="联系电话" prop="mobile">
              <Input v-model="departmentForm.mobile" placeholder="" readonly/>
            </FormItem>
            <FormItem label="所在地区" prop="area">
              <Input v-model="departmentForm.area" placeholder="" readonly/>
            </FormItem>
            <FormItem label="详细地址" prop="addr">
              <Input v-model="departmentForm.addr" placeholder="" readonly/>
            </FormItem>
            <FormItem label="门店状态" prop="shop_state">
              <i-switch size="large" v-model="departmentForm.shop_state" :true-value="1" :false-value="0">
                <span slot="open">正常营业</span>
                <span slot="close">未营业</span>
              </i-switch>
            </FormItem>
            <FormItem label="情况说明" prop="desc">
              <Input v-model="departmentForm.desc" type="text" placeholder=""/>
            </FormItem>
            <Form-item>
              <Button
                style="margin-right:5px"
                @click="submitAdd"
                :loading="submitLoading"
                type="primary"
                icon="ios-create-outline"
              >填报
              </Button>
              <Button @click="handleReset">重置</Button>
            </Form-item>
          </Form>
        </Col>
        <Col span="2">
         <div>&nbsp;</div>
        </Col>
        <Col span="7">
          <div>
            <test-video-player class="vjs-custom-skin" style="width: 95%;height: 95%;" :options="videoOptions"></test-video-player>
          </div>
        </Col>
      </Row>
    </Card>
  </div>
</template>
<script>
  import {
    departmentList,departmentInfo,videoPatrolAdd
  } from '../../../api/value';
  import './videoPatrol.css'
  import 'videojs-flash';
  import TestVideoPlayer from "../../my-components/test-video-player";
  export default {
    components: {
        TestVideoPlayer
    },
    data() {
      return {
        loading: false,
        button1: '北京',
        editTitle: '',
        modalTitle: '',
        departmentFormComponent: false,
        departmentFormAddComponent: false,
        parentTitle: "",
        expandLevel: 1,
        selectList: [],
        selectCount: 0,
        submitLoading: false,
        iconType: 0,
        iconModalVisible: false,
        departmentModalVisible: false,
        showParent: false,
        departmentForm: {
          id:'',
          department_id:'',
          title:'',
          name:'',
          mobile:'',
          area:'',
          addr:'',
          shop_state: 0,
          desc: "",
        },
        departmentFormAdd: {},
        beforeValue: '',
        data: [],
        departmentFormValidate: {
          
        },
        videoOptions: {
          playbackRates: [0.7, 1.0, 1.5, 2.0], //播放速度
          autoplay: false, //如果true,浏览器准备好时开始回放。
          muted: false, // 默认情况下将会消除任何音频。
          loop: false, // 导致视频一结束就重新开始。
          preload: 'auto', // 建议浏览器在<video>加载元素后是否应该开始下载视频数据。auto浏览器选择最佳行为,立即开始加载视频（如果浏览器支持）
          language: 'zh-CN',
          aspectRatio: '16:9', // 将播放器置于流畅模式，并在计算播放器的动态大小时使用该值。值应该代表一个比例 - 用冒号分隔的两个数字（例如"16:9"或"4:3"）
          fluid: true, // 当true时，Video.js player将拥有流体大小。换句话说，它将按比例缩放以适应其容器。
          controls: true,
          sources: [
            {
              src:
                "rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022723/0/MAIN/TCP",
              type: "rtmp/mp4"
            }
          ],
          notSupportedMessage: '此视频暂无法播放，请稍后再试', //允许覆盖Video.js无法播放媒体源时显示的默认信息。
          // poster: "./fx1.png", //你的封面地址
          controlBar: {
            timeDivider: true,
            durationDisplay: true,
            remainingTimeDisplay: false,
            fullscreenToggle: true  //全屏按钮
          }
        }
      }
    },
    methods: {
      init() {
        this.getDepartmentList();
      },
      getDepartmentList() {
        this.loading = true;
        departmentList().then(res => {
          this.loading = false;
          if (res.result) {
            this.data = res.result;
          }
        });
      },
      getDepartmentInfo(id){
        departmentInfo({id:id}).then(res => {
          let data=res.result;
          this.departmentForm.id=data.id;
          this.departmentForm.department_id=data.department_id;
          this.departmentForm.title=data.channel_name;
          this.departmentForm.name=data.applicant;
          this.departmentForm.mobile=data.mobile;
          this.departmentForm.area=data.five_name;
          this.departmentForm.addr=data.description;
        });
      },
      changeSelect(v) {
        this.selectCount = v.length;
        this.selectList = v;
      },
      handleReset() {
        this.$refs.departmentForm.resetFields();
      },
      selectTree(v) {
        if (v.length > 0) {
          // 转换null为""
          for (let attr in v[0]) {
            if (v[0][attr] === null) {
              v[0][attr] = "";
            }
          }
          let str = JSON.stringify(v[0]);
          let department = JSON.parse(str);
          this.departmentForm = department;
          this.beforeValue = this.departmentForm.component;
          this.departmentFormComponent = this.departmentForm.link_type !== 0;
          this.editTitle = department.title;
          this.getDepartmentInfo(v[0].id);
          this.videoOptions.sources.src='rtmp://125.76.229.15:1936/live/pag/125.76.229.15/7302/026123/0/MAIN/TCP';
          // TestVideoPlayer('TestVideoPlayer')
        } else {
          this.cancelEdit();
        }
      },
      cancelEdit() {
        let data = this.$refs.tree.getSelectedNodes()[0];
        if (data) {
          data.selected = false;
        }
        this.$refs.departmentForm.resetFields();
        delete this.departmentForm.id;
        this.editTitle = "";
      },
      submitAdd() {
        this.$refs.departmentForm.validate(valid => {
          if (valid) {
            this.submitLoading = true;
            videoPatrolAdd(this.departmentForm).then(res => {
              this.submitLoading = false;
              if (res.result === true) {
                this.$Message.success("添加成功");
                this.init();
                this.departmentModalVisible = false;
                this.handleReset();
              }
            });
          }
        });
      },
    },
    mounted() {
      this.init();
    }
  }
</script>
