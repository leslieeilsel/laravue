<template>
  <Card>
    <p class="btnGroup">
      <Button type="primary" @click="modal = true" icon="md-add">填报</Button>
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
    <Modal
      :mask-closable="false"
      v-model="modal"
      @on-cancel="cancel"
      :styles="{top: '20px'}"
      width="850"
      title="添加销售数据">
      <Form ref="form" :model="form" :label-width="90" :rules="formValidate">
        <Row>
          <Col span="12">
            <FormItem label="用户号码" prop="user_mobile">
              <Input v-model="form.user_mobile" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="是否新用户" prop="is_new_user" >
              <Select v-model="form.is_new_user" filterable @on-change="changeUpNewUser">
                <Option v-for="item in dict.is_new_user" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="产品类型" prop="project_type">
              <Select v-model="form.project_type" filterable>
                <Option v-for="item in dict.project_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="终端类型" prop="terminal_type">
              <Select v-model="form.terminal_type" filterable>
                <Option v-for="item in dict.terminal_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="业务类型" prop="business_type">
              <Select v-model="form.business_type" filterable>
                <Option v-for="item in dict.business_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="积分" prop="int_num">
              <Input type="text" :rows="3" v-model="form.int_num" placeholder="请输入..."/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="区域" prop="area">
              <Input type="text" :rows="3" v-model="form.area" placeholder="请输入..."/>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="填报人" prop="applicant">
              <Input v-model="form.applicant" type="text" :rows="3" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="日期" prop="date_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.date_time"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="12">
            <FormItem label="套餐类型" prop="meal_type">
              <Select v-model="form.meal_type" filterable  @on-change="changeMealType">
                <Option v-for="item in dict.meal_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="12">
            <FormItem label="套餐" prop="meal">
              <Select v-model="form.meal" filterable>
                <Option v-for="item in dict.meal" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="2">&nbsp;</col>
            <Button @click="add_set_meal" type="primary" icon="md-add"></Button>
        </Row>
        <Row v-for="(v, index) in lists" :key="index">
          <Col span="10">
            <FormItem label="升级套餐类型" :prop="'up_meal_type_'+index">
              <Select v-model="v.up_meal_type" filterable  @on-change="changeUpMealType">
                <Option v-for="item in dict.up_meal_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="10">
            <FormItem label="升级套餐" :prop="'up_meal'+index">
              <Select v-model="v.up_meal" filterable>
                <Option v-for="item in v.option_v" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="1">&nbsp;</Col>
          <Col span="3">
            <Button @click="del_set_meal(index)" type="primary" icon="md-remove"></Button>
          </Col>
        </Row>
      </Form>
      <div slot="footer" class="footer_float">
        <Button @click="handleReset('form')" :loading="loading">重置</Button>
        <Button
          @click="submitF('form')"
          :loading="submitLoading"
          type="primary"
          style="margin-left:8px"
        >保存
        </Button>
      </div>
      <div class="clear"></div>
    </Modal>
  </Card>
</template>
<script>
  import {
    salesDataAdd,
    salesDataList,dictData
  } from '../../../api/value';
  import './salesData.css'

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
            title: '用户号码',
            key: 'user_mobile',
            // fixed: 'left',
            width: 220,
          },
          {
            title: '是否新用户',
            key: 'is_new_user',
            width: 100,
            align: 'left'
          },
          {
            title: '产品类型',
            key: 'project_type',
            width: 100,
            align: "center"
          },
          {
            title: '终端类型',
            key: 'terminal_type',
            width: 200,
            align: "left"
          },
          {
            title: '业务类型',
            key: 'business_type',
            width: 200,
            align: "left"
          },
          {
            title: '套餐',
            key: 'set_meal',
            width: 300,
            align: "right"
          },
          {
            title: '积分',
            key: 'int_num',
            width: 350
          },
          {
            title: '区域',
            key: 'area',
            width: 180,
            align: "right"
          },
          {
            title: '填报人',
            key: 'applicant',
            width: 350
          },
          {
            title: '日期',
            key: 'date_time',
            width: 180
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
        modal: false,
        submitLoading: false,
        form: {
          user_mobile: '',
          is_new_user: '',
          business_type: '',
          int_num: '',
          area: '',
          applicant: '',
          project_type: '',
          meal: '',
          date_time: '',
          meal_info:''
        },
        formValidate: {
          // 表单验证规则
          user_mobile: [
            {required: true, message: "填报月份不能为空"}
          ]
        },
        dictName: {
          project_type_v: '产品类型价值',
          project_type_d: '产品类型发展',
          business_type: '业务类型',
          terminal_type:'终端类型',
          meal_type: '套餐',
          meal_0: '融合套餐',
          meal_1: '单卡套餐',
          meal_2: '智慧企业套餐',
          up_meal_type: '升级套餐',
          up_meal_0: '智慧家庭升级包',
          up_meal_1: '5G升级包',
          up_meal_2: '加第二路宽带',
          up_meal_3: '加卡',
          is_new_user: '是否新用户'
        },
        dict: {
          project_type_v:[],
          project_type_d:[],
          project_type: [],
          business_type: [],
          terminal_type:[],
          meal_type: [],
          meal: [],
          meal_0: [],
          meal_1: [],
          meal_2: [],
          up_meal_type: [],
          up_meal:[],
          up_meal_0: [],
          up_meal_1: [],
          up_meal_2: [],
          up_meal_3: [],
          is_new_user: []
          
        },
        lists:[]
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
      handleClearFiles() {
        this.$refs.upload.clearFiles();
      },
      handleSuccess(res, file) {
        this.$Message.success("导入数据成功");
        console.log(res);
        
      },
      handleFormatError(file) {
        this.$Notice.warning({
          title: '文件格式不正确',
          desc: '文件格式不正确，请选择xls或xlsx'
        });
      },
      cancel() {
        this.$refs.form.resetFields();
        this.handleClearFiles();
      },
      handleClearFiles() {
        this.$refs.upload.clearFiles();
      },
      handleReset(name) {
        this.$refs[name].resetFields();
        this.lists=[];
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            let up_meal=[];
            this.lists.forEach(function (e) {
              up_meal.push({meal:e.up_meal,meal_type:e.up_meal_type});
            });
            this.form.meal_info={
                                  meal:{meal:this.form.meal,meal_type:this.form.meal_type},
                                  up_meal:up_meal
                                }            
            salesDataAdd(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("销售数据填报成功");
                this.modal = false;
                this.cancel();
                this.lists=[];
              } else {
                this.$Message.error('销售数据填报失败!');
              }
            });
          }
        })
      },
      cancel() {
        this.$refs.form.resetFields();
      },
      getDictData() {
        dictData(this.dictName).then(res => {
          if (res.result) {
            this.dict = res.result;            
          }
        });
      },
      changeMealType(){
        if(this.form.meal_type===0){
          this.dict.meal=this.dict.meal_0;
        }else if(this.form.meal_type===1){
          this.dict.meal=this.dict.meal_1;
        }else if(this.form.meal_type===2){
          this.dict.meal=this.dict.meal_2;
        }
      },
      changeUpMealType(){
        let _this=this;
        this.lists.forEach(function (e) {
          if(e.up_meal_type===0){
            e.option_v=_this.dict.up_meal_0;
          }else if(e.up_meal_type===1){
            e.option_v=_this.dict.up_meal_1;
          }else if(e.up_meal_type===2){
            e.option_v=_this.dict.up_meal_2;
          }else if(e.up_meal_type===3){
            e.option_v=_this.dict.up_meal_3;
          }
        });
      },
      add_set_meal(){
        let len=0;
        if(this.lists.length>0){
          len=parseInt(this.lists.length)+1
        }        
        this.lists.push({'up_meal_type':'','up_meal':'','option_v':[]});
      },
      del_set_meal(index){
        this.lists.splice(index,1);
      },
      changeUpNewUser(){
        if(this.form.is_new_user===0){
          this.dict.project_type=this.dict.project_type_v;
        }else if(this.form.is_new_user===1){
          this.dict.project_type=this.dict.project_type_d;
        }
        console.log(this.dict.project_type);
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
