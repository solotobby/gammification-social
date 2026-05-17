<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['country' => 'Nigeria', 'name' => 'Nigerian Naira', 'code' => 'NGN', 'symbol' => '₦', 'base_rate' => 2015.50, 'is_active' => true],
            ['country' => 'United States', 'name' => 'United States Dollar', 'code' => 'USD', 'symbol' => '$', 'base_rate' => 1, 'is_active' => true],
            ['country' => 'United Kingdom', 'name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£', 'base_rate' => 0.75, 'is_active' => true],
            ['country' => 'Switzerland', 'name' => 'Swiss Franc', 'code' => 'CHF', 'symbol' => 'CHF', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'South Africa', 'name' => 'South African Rand', 'code' => 'ZAR', 'symbol' => 'R', 'base_rate' => 18.45, 'is_active' => false],
            ['country' => 'Kenya', 'name' => 'Kenyan Shilling', 'code' => 'KES', 'symbol' => 'KSh', 'base_rate' => 129.80, 'is_active' => false],
            ['country' => 'Ghana', 'name' => 'Ghanaian Cedi', 'code' => 'GHS', 'symbol' => 'GH₵', 'base_rate' => 15.20, 'is_active' => false],
            ['country' => 'Egypt', 'name' => 'Egyptian Pound', 'code' => 'EGP', 'symbol' => 'E£', 'base_rate' => 48.30, 'is_active' => false],
            ['country' => 'Morocco', 'name' => 'Moroccan Dirham', 'code' => 'MAD', 'symbol' => 'MAD', 'base_rate' => 9.95, 'is_active' => false],
            ['country' => 'Uganda', 'name' => 'Ugandan Shilling', 'code' => 'UGX', 'symbol' => 'USh', 'base_rate' => 3720.00, 'is_active' => false],
            ['country' => 'Tanzania', 'name' => 'Tanzanian Shilling', 'code' => 'TZS', 'symbol' => 'TSh', 'base_rate' => 2580.00, 'is_active' => false],
            ['country' => 'Ethiopia', 'name' => 'Ethiopian Birr', 'code' => 'ETB', 'symbol' => 'Br', 'base_rate' => 57.80, 'is_active' => false],
            ['country' => 'Rwanda', 'name' => 'Rwandan Franc', 'code' => 'RWF', 'symbol' => 'FRw', 'base_rate' => 1325.00, 'is_active' => false],
            ['country' => 'Algeria', 'name' => 'Algerian Dinar', 'code' => 'DZD', 'symbol' => 'دج', 'base_rate' => 134.60, 'is_active' => false],
            ['country' => 'Tunisia', 'name' => 'Tunisian Dinar', 'code' => 'TND', 'symbol' => 'DT', 'base_rate' => 3.12, 'is_active' => false],
            ['country' => 'Zambia', 'name' => 'Zambian Kwacha', 'code' => 'ZMW', 'symbol' => 'ZK', 'base_rate' => 27.10, 'is_active' => false],
            ['country' => 'Botswana', 'name' => 'Botswana Pula', 'code' => 'BWP', 'symbol' => 'P', 'base_rate' => 13.65, 'is_active' => false],
            ['country' => 'Namibia', 'name' => 'Namibian Dollar', 'code' => 'NAD', 'symbol' => 'N$', 'base_rate' => 18.45, 'is_active' => false],
            ['country' => 'Mozambique', 'name' => 'Mozambican Metical', 'code' => 'MZN', 'symbol' => 'MTn', 'base_rate' => 63.50, 'is_active' => false],
            ['country' => 'Gabon', 'name' => 'Central African CFA Franc', 'code' => 'XAF', 'symbol' => 'FCFA', 'base_rate' => 550.00, 'is_active' => false],
            ['country' => 'Seychelles', 'name' => 'Seychellois Rupee', 'code' => 'SCR', 'symbol' => 'SR', 'base_rate' => 13.50, 'is_active' => false],
            ['country' => 'Central African Republic', 'name' => 'Central African CFA Franc', 'code' => 'XAF', 'symbol' => 'FCFA', 'base_rate' => 550.00, 'is_active' => false],
            ['country' => 'Chad', 'name' => 'Central African CFA Franc', 'code' => 'XAF', 'symbol' => 'FCFA', 'base_rate' => 550.00, 'is_active' => false],
            ['country' => 'Republic of the Congo', 'name' => 'Central African CFA Franc', 'code' => 'XAF', 'symbol' => 'FCFA', 'base_rate' => 550.00, 'is_active' => false],
            ['country' => 'Equatorial Guinea', 'name' => 'Central African CFA Franc', 'code' => 'XAF', 'symbol' => 'FCFA', 'base_rate' => 550.00, 'is_active' => false],
            ['country' => 'Cameroon', 'name' => 'Central African CFA Franc', 'code' => 'XAF', 'symbol' => 'FCFA', 'base_rate' => 550.00, 'is_active' => false],
            ['country' => 'France', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Spain', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Italy', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Belgium', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Finland', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Ireland', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Norway', 'name' => 'Norwegian Krone', 'code' => 'NOK', 'symbol' => 'kr', 'base_rate' => 10.75, 'is_active' => false],
            ['country' => 'Sweden', 'name' => 'Swedish Krona', 'code' => 'SEK', 'symbol' => 'kr', 'base_rate' => 10.60, 'is_active' => false],
            ['country' => 'Denmark', 'name' => 'Danish Krone', 'code' => 'DKK', 'symbol' => 'kr', 'base_rate' => 6.87, 'is_active' => false],
            ['country' => 'Poland', 'name' => 'Polish Zloty', 'code' => 'PLN', 'symbol' => 'zł', 'base_rate' => 4.05, 'is_active' => false],
            ['country' => 'Czech Republic', 'name' => 'Czech Koruna', 'code' => 'CZK', 'symbol' => 'Kč', 'base_rate' => 22.00, 'is_active' => false],
            ['country' => 'Hungary', 'name' => 'Hungarian Forint', 'code' => 'HUF', 'symbol' => 'Ft', 'base_rate' => 295.00, 'is_active' => false],
            ['country' => 'Greece', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Portugal', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Netherlands', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Austria', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Luxembourg', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Malta', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
            ['country' => 'Slovakia', 'name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'base_rate' => 0.92, 'is_active' => false],
          

            // North America
           
            ['country' => 'Canada', 'name' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => 'C$', 'base_rate' => 1.37, 'is_active' => false],
            ['country' => 'Mexico', 'name' => 'Mexican Peso', 'code' => 'MXN', 'symbol' => '$', 'base_rate' => 16.85, 'is_active' => false],
            ['country' => 'Jamaica', 'name' => 'Jamaican Dollar', 'code' => 'JMD', 'symbol' => 'J$', 'base_rate' => 156.30, 'is_active' => false],
            ['country' => 'Cuba', 'name' => 'Cuban Peso', 'code' => 'CUP', 'symbol' => '$', 'base_rate' => 24.00, 'is_active' => false],

            // South America
            ['country' => 'Brazil', 'name' => 'Brazilian Real', 'code' => 'BRL', 'symbol' => 'R$', 'base_rate' => 5.12, 'is_active' => false],
            ['country' => 'Argentina', 'name' => 'Argentine Peso', 'code' => 'ARS', 'symbol' => '$', 'base_rate' => 890.00, 'is_active' => false],
            ['country' => 'Chile', 'name' => 'Chilean Peso', 'code' => 'CLP', 'symbol' => '$', 'base_rate' => 945.00, 'is_active' => false],
            ['country' => 'Colombia', 'name' => 'Colombian Peso', 'code' => 'COP', 'symbol' => '$', 'base_rate' => 3920.00, 'is_active' => false],
            ['country' => 'Peru', 'name' => 'Peruvian Sol', 'code' => 'PEN', 'symbol' => 'S/', 'base_rate' => 3.72, 'is_active' => false],

            // Asia
            ['country' => 'China', 'name' => 'Chinese Yuan', 'code' => 'CNY', 'symbol' => '¥', 'base_rate' => 7.24, 'is_active' => false],
            ['country' => 'Japan', 'name' => 'Japanese Yen', 'code' => 'JPY', 'symbol' => '¥', 'base_rate' => 155.40, 'is_active' => false],
            ['country' => 'India', 'name' => 'Indian Rupee', 'code' => 'INR', 'symbol' => '₹', 'base_rate' => 83.10, 'is_active' => false],
            ['country' => 'Pakistan', 'name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => '₨', 'base_rate' => 278.00, 'is_active' => false],
            ['country' => 'Bangladesh', 'name' => 'Bangladeshi Taka', 'code' => 'BDT', 'symbol' => '৳', 'base_rate' => 117.00, 'is_active' => false],
            ['country' => 'Indonesia', 'name' => 'Indonesian Rupiah', 'code' => 'IDR', 'symbol' => 'Rp', 'base_rate' => 16120.00, 'is_active' => false],
            ['country' => 'Malaysia', 'name' => 'Malaysian Ringgit', 'code' => 'MYR', 'symbol' => 'RM', 'base_rate' => 4.70, 'is_active' => false],
            ['country' => 'Singapore', 'name' => 'Singapore Dollar', 'code' => 'SGD', 'symbol' => 'S$', 'base_rate' => 1.35, 'is_active' => false],
            ['country' => 'Thailand', 'name' => 'Thai Baht', 'code' => 'THB', 'symbol' => '฿', 'base_rate' => 36.50, 'is_active' => false],
            ['country' => 'South Korea', 'name' => 'South Korean Won', 'code' => 'KRW', 'symbol' => '₩', 'base_rate' => 1375.00, 'is_active' => false],
            ['country' => 'Vietnam', 'name' => 'Vietnamese Dong', 'code' => 'VND', 'symbol' => '₫', 'base_rate' => 25450.00, 'is_active' => false],
            ['country' => 'Philippines', 'name' => 'Philippine Peso', 'code' => 'PHP', 'symbol' => '₱', 'base_rate' => 57.20, 'is_active' => false],
            ['country' => 'Saudi Arabia', 'name' => 'Saudi Riyal', 'code' => 'SAR', 'symbol' => '﷼', 'base_rate' => 3.75, 'is_active' => false],
            ['country' => 'United Arab Emirates', 'name' => 'UAE Dirham', 'code' => 'AED', 'symbol' => 'د.إ', 'base_rate' => 3.67, 'is_active' => false],
            ['country' => 'Qatar', 'name' => 'Qatari Riyal', 'code' => 'QAR', 'symbol' => '﷼', 'base_rate' => 3.64, 'is_active' => false],
            ['country' => 'Israel', 'name' => 'Israeli New Shekel', 'code' => 'ILS', 'symbol' => '₪', 'base_rate' => 3.70, 'is_active' => false],
            ['country' => 'Turkey', 'name' => 'Turkish Lira', 'code' => 'TRY', 'symbol' => '₺', 'base_rate' => 32.20, 'is_active' => false],

            // Oceania
            ['country' => 'Australia', 'name' => 'Australian Dollar', 'code' => 'AUD', 'symbol' => 'A$', 'base_rate' => 1.52, 'is_active' => false],
            ['country' => 'New Zealand', 'name' => 'New Zealand Dollar', 'code' => 'NZD', 'symbol' => 'NZ$', 'base_rate' => 1.65, 'is_active' => false],
            ['country' => 'Fiji', 'name' => 'Fijian Dollar', 'code' => 'FJD', 'symbol' => 'FJ$', 'base_rate' => 2.25, 'is_active' => false],

            // Caribbean & Central America
            ['country' => 'Bahamas', 'name' => 'Bahamian Dollar', 'code' => 'BSD', 'symbol' => '$', 'base_rate' => 1.00, 'is_active' => false],
            ['country' => 'Barbados', 'name' => 'Barbadian Dollar', 'code' => 'BBD', 'symbol' => '$', 'base_rate' => 2.00, 'is_active' => false],
            ['country' => 'Costa Rica', 'name' => 'Costa Rican Colon', 'code' => 'CRC', 'symbol' => '₡', 'base_rate' => 510.00, 'is_active' => false],
            ['country' => 'Dominican Republic', 'name' => 'Dominican Peso', 'code' => 'DOP', 'symbol' => 'RD$', 'base_rate' => 59.00, 'is_active' => false],
            ['country' => 'Guatemala', 'name' => 'Guatemalan Quetzal', 'code' => 'GTQ', 'symbol' => 'Q', 'base_rate' => 7.75, 'is_active' => false],

            // Eastern Europe & Eurasia
            ['country' => 'Russia', 'name' => 'Russian Ruble', 'code' => 'RUB', 'symbol' => '₽', 'base_rate' => 91.00, 'is_active' => false],
            ['country' => 'Ukraine', 'name' => 'Ukrainian Hryvnia', 'code' => 'UAH', 'symbol' => '₴', 'base_rate' => 40.20, 'is_active' => false],
            ['country' => 'Kazakhstan', 'name' => 'Kazakhstani Tenge', 'code' => 'KZT', 'symbol' => '₸', 'base_rate' => 445.00, 'is_active' => false],

            // Additional Countries
            ['country' => 'Nepal', 'name' => 'Nepalese Rupee', 'code' => 'NPR', 'symbol' => '₨', 'base_rate' => 133.00, 'is_active' => false],
            ['country' => 'Sri Lanka', 'name' => 'Sri Lankan Rupee', 'code' => 'LKR', 'symbol' => 'Rs', 'base_rate' => 299.00, 'is_active' => false],
            ['country' => 'Myanmar', 'name' => 'Burmese Kyat', 'code' => 'MMK', 'symbol' => 'Ks', 'base_rate' => 2100.00, 'is_active' => false],
            ['country' => 'Cambodia', 'name' => 'Cambodian Riel', 'code' => 'KHR', 'symbol' => '៛', 'base_rate' => 4050.00, 'is_active' => false],
            ['country' => 'Laos', 'name' => 'Lao Kip', 'code' => 'LAK', 'symbol' => '₭', 'base_rate' => 21600.00, 'is_active' => false],
            ['country' => 'Mongolia', 'name' => 'Mongolian Tögrög', 'code' => 'MNT', 'symbol' => '₮', 'base_rate' => 3450.00, 'is_active' => false],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
        
    }
}
