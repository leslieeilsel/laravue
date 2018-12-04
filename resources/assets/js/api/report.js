import request from '../utils/request'

/**
 * 概览表 - 月报
 *
 * @param startMonth 开始时间
 * @param endMonth 结束时间
 * @param reportType 报表类型
 * @returns {*}
 */
export function getOverviewMonthData(startMonth, endMonth, reportType) {
  return request({
    url: '/api/overviewmonth',
    method: 'post',
    data: {
      startMonth,
      endMonth,
      reportType
    }
  })
}
