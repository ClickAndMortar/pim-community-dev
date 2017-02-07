<?php

namespace spec\Pim\Component\Catalog\Factory\ProductValue;

use PhpSpec\ObjectBehavior;
use Pim\Component\Catalog\Exception\InvalidArgumentException;
use Pim\Component\Catalog\Factory\MetricFactory;
use Pim\Component\Catalog\Factory\ProductValue\MetricProductValueFactory;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\MetricInterface;
use Pim\Component\Catalog\Model\ProductValue;
use Prophecy\Argument;

class MetricProductValueFactorySpec extends ObjectBehavior
{
    function let(MetricFactory $metricFactory)
    {
        $this->beConstructedWith($metricFactory, ProductValue::class, 'pim_catalog_metric');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MetricProductValueFactory::class);
    }

    function it_supports_metric_attribute_type()
    {
        $this->supports('foo')->shouldReturn(false);
        $this->supports('pim_catalog_metric')->shouldReturn(true);
    }

    function it_creates_an_empty_metric_product_value(
        $metricFactory,
        AttributeInterface $attribute,
        MetricInterface $metric
    ) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('metric_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_metric');
        $attribute->getBackendType()->willReturn('metric');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $attribute->getMetricFamily()->willReturn('Length');
        $metricFactory->createMetric('Length', null, null)->willReturn($metric);
        $metric->getData()->willReturn(null);
        $metric->getUnit()->willReturn(null);
        $metric->getFamily()->willReturn('Length');

        $productValue = $this->create(
            $attribute,
            null,
            null,
            null
        );

        $productValue->shouldReturnAnInstanceOf(ProductValue::class);
        $productValue->shouldHaveAttribute('metric_attribute');
        $productValue->shouldNotBeLocalizable();
        $productValue->shouldNotBeScopable();
        $productValue->shouldBeEmpty();
    }

    function it_creates_a_localizable_and_scopable_empty_metric_product_value(
        $metricFactory,
        AttributeInterface $attribute,
        MetricInterface $metric
    ) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('metric_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_metric');
        $attribute->getBackendType()->willReturn('metric');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $attribute->getMetricFamily()->willReturn('Length');
        $metricFactory->createMetric('Length', null, null)->willReturn($metric);
        $metric->getData()->willReturn(null);
        $metric->getUnit()->willReturn(null);
        $metric->getFamily()->willReturn('Length');

        $productValue = $this->create(
            $attribute,
            'ecommerce',
            'en_US',
            null
        );

        $productValue->shouldReturnAnInstanceOf(ProductValue::class);
        $productValue->shouldHaveAttribute('metric_attribute');
        $productValue->shouldBeLocalizable();
        $productValue->shouldHaveLocale('en_US');
        $productValue->shouldBeScopable();
        $productValue->shouldHaveChannel('ecommerce');
        $productValue->shouldBeEmpty();
    }

    function it_creates_a_metric_product_value(
        $metricFactory,
        AttributeInterface $attribute,
        MetricInterface $metric
    ) {
        $attribute->isScopable()->willReturn(false);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->getCode()->willReturn('metric_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_metric');
        $attribute->getBackendType()->willReturn('metric');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $attribute->getMetricFamily()->willReturn('Length');
        $metricFactory->createMetric('Length', 'GRAM', 42)->willReturn($metric);
        $metric->getData()->willReturn(42);
        $metric->getUnit()->willReturn('GRAM');
        $metric->getFamily()->willReturn('Length');

        $productValue = $this->create(
            $attribute,
            null,
            null,
            ['amount' => 42, 'unit' => 'GRAM']
        );

        $productValue->shouldReturnAnInstanceOf(ProductValue::class);
        $productValue->shouldHaveAttribute('metric_attribute');
        $productValue->shouldNotBeLocalizable();
        $productValue->shouldNotBeScopable();
        $productValue->shouldHaveMetric(['amount' => 42, 'unit' => 'GRAM']);
    }

    function it_creates_a_localizable_and_scopable_metric_product_value(
        $metricFactory,
        AttributeInterface $attribute,
        MetricInterface $metric
    ) {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('metric_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_metric');
        $attribute->getBackendType()->willReturn('metric');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $attribute->getMetricFamily()->willReturn('Length');
        $metricFactory->createMetric('Length', 'GRAM', 42)->willReturn($metric);
        $metric->getData()->willReturn(42);
        $metric->getUnit()->willReturn('GRAM');
        $metric->getFamily()->willReturn('Length');

        $productValue = $this->create(
            $attribute,
            'ecommerce',
            'en_US',
            ['amount' => 42, 'unit' => 'GRAM']
        );

        $productValue->shouldReturnAnInstanceOf(ProductValue::class);
        $productValue->shouldHaveAttribute('metric_attribute');
        $productValue->shouldBeLocalizable();
        $productValue->shouldHaveLocale('en_US');
        $productValue->shouldBeScopable();
        $productValue->shouldHaveChannel('ecommerce');
        $productValue->shouldHaveMetric(['amount' => 42, 'unit' => 'GRAM']);
    }

    function it_throws_an_exception_if_provided_data_is_not_an_array($metricFactory, AttributeInterface $attribute)
    {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('metric_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_metric');
        $attribute->getBackendType()->willReturn('metric');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $attribute->getMetricFamily()->shouldNotBeCalled();
        $metricFactory->createMetric(Argument::cetera())->shouldNotBeCalled();

        $exception = InvalidArgumentException::arrayExpected(
            'metric_attribute',
            'metric',
            'factory',
            'string'
        );

        $this
            ->shouldThrow($exception)
            ->during('create', [$attribute, 'ecommerce', 'en_US', 'foobar']);
    }

    function it_throws_an_exception_if_provided_data_has_no_amount($metricFactory, AttributeInterface $attribute)
    {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('metric_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_metric');
        $attribute->getBackendType()->willReturn('metric');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $attribute->getMetricFamily()->shouldNotBeCalled();
        $metricFactory->createMetric(Argument::cetera())->shouldNotBeCalled();

        $exception = InvalidArgumentException::arrayKeyExpected(
            'metric_attribute',
            'amount',
            'metric',
            'factory',
            'foo, unit'
        );

        $this
            ->shouldThrow($exception)
            ->during('create', [$attribute, 'ecommerce', 'en_US', ['foo' => 42, 'unit' => 'GRAM']]);
    }

    function it_throws_an_exception_if_provided_data_has_no_unit($metricFactory, AttributeInterface $attribute)
    {
        $attribute->isScopable()->willReturn(true);
        $attribute->isLocalizable()->willReturn(true);
        $attribute->getCode()->willReturn('metric_attribute');
        $attribute->getAttributeType()->willReturn('pim_catalog_metric');
        $attribute->getBackendType()->willReturn('metric');
        $attribute->isBackendTypeReferenceData()->willReturn(false);

        $attribute->getMetricFamily()->shouldNotBeCalled();
        $metricFactory->createMetric(Argument::cetera())->shouldNotBeCalled();

        $exception = InvalidArgumentException::arrayKeyExpected(
            'metric_attribute',
            'unit',
            'metric',
            'factory',
            'amount, bar'
        );

        $this
            ->shouldThrow($exception)
            ->during('create', [$attribute, 'ecommerce', 'en_US', ['amount' => 42, 'bar' => 'GRAM']]);
    }

    public function getMatchers()
    {
        return [
            'haveAttribute' => function ($subject, $attributeCode) {
                return $subject->getAttribute()->getCode() === $attributeCode;
            },
            'beLocalizable' => function ($subject) {
                return null !== $subject->getLocale();
            },
            'haveLocale'    => function ($subject, $localeCode) {
                return $localeCode === $subject->getLocale();
            },
            'beScopable'    => function ($subject) {
                return null !== $subject->getScope();
            },
            'haveChannel'   => function ($subject, $channelCode) {
                return $channelCode === $subject->getScope();
            },
            'beEmpty'       => function ($subject) {
                $metric = $subject->getData();

                return null === $metric->getData() && null === $metric->getUnit();
            },
            'haveMetric'    => function ($subject, $expectedMetric) {
                $metric = $subject->getData();

                return $expectedMetric['amount'] === $metric->getData() &&
                    $expectedMetric['unit'] === $metric->getUnit();
            },
        ];
    }
}