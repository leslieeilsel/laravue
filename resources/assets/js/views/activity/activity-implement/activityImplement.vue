<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">活动执行填报</Button>
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
      title="活动执行填报">
      <Form ref="form" :model="form" :label-width="160">
        <Row>
          <Col span="12">
            <FormItem label="活动名称" prop="name">
              <Input v-model="form.name" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="活动位置" prop="position">
              <Input v-model="form.position" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="区域" prop="area">
              <Input v-model="form.area" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="负责人" prop="applicant">
              <Input v-model="form.applicant" placeholder=""></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="活动时间" prop="date_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.date_time"></DatePicker>
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
    activityImplementAdd,
    activityImplement
  } from '../../../api/value';
  import './activityImplement.css'

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
            title: '活动名称',
            key: 'name',
            width: 100,
            // fixed: 'left',
            align: "center"
          },
          {
            title: '活动位置',
            key: 'position',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '区域',
            key: 'area',
            width: 100,
            align: 'left'
          },
          {
            title: '负责人',
            key: 'applicant',
            width: 100,
            align: "center"
          },
          {
            title: '活动时间',
            key: 'date_time',
            width: 200,
            align: "left"
          },
          {
            title: '图片',
            key: 'pic',
            width: 200,
            align: "left"
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
        upbtnPicDisabled:false,
        loading:true,
        modal: false,
        form: {
          name: '',
          position: '',
          area: '',
          applicant: '',
          date_time: '',
          pic: '',
        }
      }
    },
    methods: {
      init() {
        this.getActivityImplement();
      },
      getActivityImplement() {
        this.tableLoading = true;
        activityImplement(this.searchForm).then(res => {
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
            activityImplementAdd(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("活动执行填报成功");
                this.modal = false;
                this.init();
              } else {
                this.$Message.error('活动执行填报失败!');
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
      handleSuccessPic(res, file) {
        if (this.form.pic) {
          this.form.pic = this.form.pic + ',' + res.result;
        } else {
          this.form.pic = res.result;
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
