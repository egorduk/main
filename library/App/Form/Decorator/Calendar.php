<?php

/**
 * App_Decorator_Calendar
 * 
 * Декоратор отображения javascript календаря
 * 
 */
class App_Form_Decorator_Calendar extends Zend_Form_Decorator_Abstract
{
    /**
     * Получение строк подключения Javacript и CSS для календаря
     * Статическая переменная $jsAndCss отвечает за то, чтобу подключение
     * осуществлялось только один раз
     *
     * @return string
     */
    private function _getJsAndCss()
    {
        static $jsAndCss = null;
        
        if($jsAndCss === null) {

            $jsAndCss = '
            <style type="text/css">@import url(js/calendar/skins/aqua/theme.css);</style>
            <script type="text/javascript" src="js/calendar/calendar.js"></script>
            <script type="text/javascript" src="js/calendar/lang/calendar-ru.js"></script>
            <script type="text/javascript" src="js/calendar/calendar-setup.js"></script>
            ';
            
            return $jsAndCss;
        }
        return '';
    }
    
    
    /**
     * Получение кода ссылки и изображения каледаря. Настройка календаря
     *
     * @return string
     */
    private function _getCalendarLink()
    {

        $calendarLink = '
            <a href="#" id="' . $this->getElement()->getName() . '_calendar">
                <img class="calendar-image" src = "js/calendar/calendar.gif">
            </a>
    
            <script type="text/javascript">
                Calendar.setup(
                  {
                    inputField  : "' . $this->getElement()->getName() . '",
                    ifFormat    : "%d.%m.%Y",
                    button      : "' . $this->getElement()->getName() . '_calendar",
                    firstDay    : 1
                  }
                );
            </script>
        ';
        
        return $calendarLink;
    }
    
    
    /**
     * Рендеринг декоратора
     *
     * @param string $content
     * @return string
     */
    public function render($content)
    {
        // Получаем объект элемента к которому применяется декоратор
        $element = $this->getElement();
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        // Проверяем объект вида зарегистрированного для формы
        if (null === $element->getView()) {
            return $content;
        }
        
        // Расположение декоратора, "после" или "перед" элементом, по умолчанию "после"
        $placement = $this->getPlacement();
        // Разделитель между элементом и декоратором
        $separator = $this->getSeparator();
        
        // Взависимости от настроек расположения декоратора возвращаем содержимое
        switch  ($placement) {
            // После элемента
            case  'APPEND':
                return $this->_getJsAndCss() . $content . $separator . $this->_getCalendarLink();
            // Перед элементом
            case  'PREPEND':
                return $this->_getJsAndCss() . $this->_getCalendarLink() . $separator . $content;
            case  null:
            // По умолчанию просто возвращаем содержимое календаря
            default:
                return $this->_getJsAndCss() . $this->_getCalendarLink();
        }

    }
}