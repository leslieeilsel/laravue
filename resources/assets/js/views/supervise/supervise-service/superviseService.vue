<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">服务信息填报</Button>
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
            <FormItem label="用户名" prop="ename">
              <Input v-model="form.ename" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="工号" prop="job_num">
              <Input v-model="form.mobile" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="设备号" prop="device_num">
              <Input v-model="form.device_num" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="区域" prop="area">
              <Input v-model="form.area" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <FormItem label="图片" prop="pic">
            <Upload
              ref="upload"
              :disabled="upbtnPicDisabled"
              name="pic"
              :on-success="handleSuccessPic"
              multiple
              :data="upData"
              :format="['jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'pdf']"
              :max-size="600"
              :on-format-error="handleFormatError"
              :on-exceeded-size="handleMaxSize"
              action="/api/value/uploadPic">
              <Button icon="ios-cloud-upload-outline">上传</Button>
              <div style="color:#ea856b">文件大小不能超过600KB,请确保上传完毕之后再提交保存</div>
            </Upload>
          </FormItem>
        </Row>
        <Row>
          <FormItem label="视频" prop="video">
            <Upload
              ref="upload"
              :disabled="upbtnVideoDisabled"
              name="video"
              :on-success="handleSuccessVideo"
              multiple
              :data="upData"
              :format="['mp4']"
              :on-format-error="handleFormatError"
              action="/api/value/uploadVideo">
              <Button icon="ios-cloud-upload-outline">上传</Button>
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
  </Card>
</template>
<script>
  import {
    salesDataAdd,
    salesDataList,dictData
  } from '../../../api/value';
  import './superviseService.css'

  export default {
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
            title: '用户名',
            key: 'ename',
            width: 100,
            // fixed: 'left',
            align: "center"
          },
          {
            title: '工号',
            key: 'job_num',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '设备号',
            key: 'device_num',
            width: 100,
            align: 'left'
          },
          {
            title: '区域',
            key: 'area',
            width: 100,
            align: "center"
          },
          {
            title: '图片',
            key: 'pic',
            width: 200,
            align: "left"
          },
          {
            title: '视频',
            key: 'video',
            width: 100,
            align: "right"
          }
        ],
        data: [],
        tableLoading: true,
        loading: false,
        searchForm: {
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        submitLoading: false,
        loading:true,
        modal: false,
        form: {
          ename: '',
          job_num: '',
          device_num: '',
          area: '',
          integral: '',
          area: '',
          pic: '',
          video: ''
        },
        dictName: {
          product_type: '产品类型',
          business_type: '业务类型',
          meal: '套餐',
          is_new_user: '是否新用户'
        },
        dict: {
          product_type: [],
          business_type: [],
          meal: [],
          is_new_user: []
        },
        upbtnPicDisabled: false,
        upbtnVideoDisabled: false,
      }
    },
    methods: {
      init() {
        this.getSalesDataList();
        this.getDictData();
      },
      getSalesDataList() {
        this.tableLoading = true;
        salesDataList(this.searchForm).then(res => {
          this.tableLoading = false;
          this.data = res.result;
          this.dataCount = res.total;
          this.searchForm.pageNumber = 1;
          this.searchForm.pageSize = 10;
        })
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getSalesDataList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getSalesDataList();
      },
      handleReset(name) {
        this.$refs[name].resetFields();
        this.$refs.upload.clearFiles();
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            salesDataAdd(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("服务信息填报成功");
                this.modal = false;
                this.init();
              } else {
                this.$Message.error('服务信息填报失败!');
              }
            });
          }
        })
      },
      cancel() {
        this.$refs.form.resetFields();
        this.handleClearFiles();
      },
      handleClearFiles() {
        this.$refs.upload.clearFiles();
      },
      getDictData() {
        dictData(this.dictName).then(res => {
          console.log(res);
          if (res.result) {
            this.dict = res.result;
          }
        });
      },
      handleSuccessPic(res, file) {
        if (this.form.pic) {
          this.form.pic = this.form.pic + ',' + res.result;
        } else {
          this.form.pic = res.result;
        }
      },
      handleSuccessVideo(res, file) {
        if (this.form.video) {
          this.form.video = this.form.video + ',' + res.result;
        } else {
          this.form.video = res.result;
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
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
